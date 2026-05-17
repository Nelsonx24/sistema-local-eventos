/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { useState, useEffect, useRef } from 'react';
import { 
  LayoutDashboard, 
  CalendarDays, 
  Package, 
  DollarSign, 
  Users, 
  PieChart, 
  Settings 
} from 'lucide-react';
import { Sidebar } from './components/Sidebar';
import { Header } from './components/Header';
import { DashboardView } from './components/DashboardView';
import { EventsView } from './components/EventsView';
import { InventoryView } from './components/InventoryView';
import { SalesView } from './components/SalesView';
import { StaffView } from './components/StaffView';
import { SettingsView } from './components/SettingsView';
import { ReportsView } from './components/ReportsView';
import { OthersView } from './components/OthersView';
import { LoginView } from './components/LoginView';
import { InventoryItem, SaleRecord, EventRecord, StaffRole, EventType, StaffMember } from './types';
import { auth } from './lib/firebase';
import { onAuthStateChanged, signOut } from 'firebase/auth';
import { 
  subscribeToCollection, 
  addDocument, 
  updateDocument, 
  deleteDocument,
  testConnection 
} from './services/firebaseService';
import { doc, getDoc, setDoc } from 'firebase/firestore';
import { db } from './lib/firebase';

const MENU_ITEMS = [
  { id: 'dashboard', label: 'Inicio', icon: LayoutDashboard, roles: [StaffRole.ADMIN] },
  { id: 'events', label: 'Eventos', icon: CalendarDays, roles: [StaffRole.ADMIN, StaffRole.SELLER, StaffRole.CM] },
  { id: 'inventory', label: 'Inventario', icon: Package, roles: [StaffRole.ADMIN] },
  { id: 'sales', label: 'Ventas', icon: DollarSign, roles: [StaffRole.ADMIN, StaffRole.SELLER] },
  { id: 'staff', label: 'Personal', icon: Users, roles: [StaffRole.ADMIN] },
  { id: 'reports', label: 'Reportes', icon: PieChart, roles: [StaffRole.ADMIN, StaffRole.SELLER, StaffRole.CM] },
  { id: 'others', label: 'Otros', icon: Settings, roles: [StaffRole.ADMIN, StaffRole.CM] },
  { id: 'settings', label: 'Configuración', icon: Settings, roles: [StaffRole.ADMIN] },
];

export default function App() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [currentView, setCurrentView] = useState('events');
  const [userRole, setUserRole] = useState<StaffRole>(StaffRole.ADMIN);
  const [userName, setUserName] = useState('');
  const [qrImage, setQrImage] = useState('https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=Demo-Payment');
  const [inventory, setInventory] = useState<InventoryItem[]>([]);
  const [sales, setSales] = useState<SaleRecord[]>([]);
  const [events, setEvents] = useState<EventRecord[]>([]);
  const [staff, setStaff] = useState<StaffMember[]>([]);
  const [eventTypes, setEventTypes] = useState<string[]>(Object.values(EventType));
  const [isInitializing, setIsInitializing] = useState(true);

  useEffect(() => {
    testConnection();

    const unsubscribeAuth = onAuthStateChanged(auth, async (user) => {
      if (user) {
        setIsAuthenticated(true);
        setUserName(user.displayName || user.email || 'Usuario');
      } else {
        setIsAuthenticated(false);
      }
      setIsInitializing(false);
    });

    return () => unsubscribeAuth();
  }, []);

  const eventsRef = useRef<EventRecord[]>([]);

  useEffect(() => {
    if (!isAuthenticated) return;

    let isInitialEventsLoad = true;
    const unsubEvents = subscribeToCollection('events', (data) => {
      const newEvents = data as EventRecord[];
      if (!isInitialEventsLoad && newEvents.length > eventsRef.current.length) {
        const added = newEvents.filter(ne => !eventsRef.current.some(oe => oe.id === ne.id));
        added.forEach(event => {
          if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Nuevo Evento Registrado', {
              body: `${event.clientName} - ${event.eventType} para el ${event.date}`,
              icon: '/favicon.ico'
            });
          }
        });
      }
      setEvents(newEvents);
      eventsRef.current = newEvents;
      isInitialEventsLoad = false;
    });

    const unsubInventory = subscribeToCollection('inventory', (data) => setInventory(data as InventoryItem[]));
    const unsubSales = subscribeToCollection('sales', (data) => setSales(data as SaleRecord[]));
    const unsubStaff = subscribeToCollection('staff', (data) => setStaff(data as StaffMember[]));
    
    // Load config
    const loadConfig = async () => {
      const qrDoc = await getDoc(doc(db, 'config', 'qr'));
      if (qrDoc.exists()) {
        setQrImage(qrDoc.data().url);
      }
      
      const typesDoc = await getDoc(doc(db, 'config', 'eventTypes'));
      if (typesDoc.exists()) {
        setEventTypes(typesDoc.data().list);
      }
    };
    loadConfig();

    return () => {
      unsubEvents();
      unsubInventory();
      unsubSales();
      unsubStaff();
    };
  }, [isAuthenticated]);

  const handleProcessSale = async (newSale: SaleRecord) => {
    const saleWithMeta = {
      ...newSale,
      sellerName: newSale.sellerName || userName,
      date: newSale.date || new Date().toISOString()
    };

    // 1. Add Sale to Firestore
    await addDocument('sales', saleWithMeta);

    // 2. Update Inventory in Firestore
    for (const saleItem of saleWithMeta.items) {
      const item = inventory.find(i => i.name === saleItem.name);
      if (!item) continue;

      const updatedItem = { ...item };
      if (saleItem.type === 'Caja') {
        updatedItem.boxes = Math.max(0, updatedItem.boxes - saleItem.quantity);
      } else {
        let unitsToSubtract = saleItem.quantity;
        while (unitsToSubtract > 0) {
          if (updatedItem.looseUnits >= unitsToSubtract) {
            updatedItem.looseUnits -= unitsToSubtract;
            unitsToSubtract = 0;
          } else {
            if (updatedItem.boxes > 0) {
              updatedItem.boxes -= 1;
              updatedItem.looseUnits += updatedItem.unitsPerBox;
            } else {
              updatedItem.looseUnits = 0;
              unitsToSubtract = 0;
            }
          }
        }
      }
      const { id: itemId, ...itemData } = updatedItem;
      await updateDocument('inventory', itemId, itemData);
    }
  };

  const handleUpdateEvents = async (newEvents: EventRecord[]) => {
    // This is tricky because the components expect a "setEvents" style setter
    // For specific additions/edits, we should update Firestore
    // However, looking at how components use this, many pass a function to setEvents
  };

  const handleAddEvent = async (event: Omit<EventRecord, 'id'>) => {
    await addDocument('events', event);
  };

  const handleUpdateEvent = async (id: string, event: Partial<EventRecord>) => {
    await updateDocument('events', id, event);
  };

  const handleDeleteEvent = async (id: string) => {
    await deleteDocument('events', id);
  };

  const handleDeleteInventory = async (id: string) => {
    await deleteDocument('inventory', id);
  };

  const handleDeleteSale = async (id: string) => {
    await deleteDocument('sales', id);
  };

  const updateQrImage = async (url: string) => {
    setQrImage(url);
    await setDoc(doc(db, 'config', 'qr'), { url });
  };

  const renderView = () => {
    switch (currentView) {
      case 'dashboard':
        return <DashboardView />;
      case 'events':
        return (
          <EventsView 
            events={events} 
            onDeleteEvent={handleDeleteEvent} 
            userRole={userRole} 
            userName={userName}
            eventTypes={eventTypes}
          />
        );
      case 'inventory':
        return <InventoryView inventory={inventory} onDeleteInventory={handleDeleteInventory} userRole={userRole} />;
      case 'sales':
        return <SalesView sales={sales} inventory={inventory} events={events} onProcessSale={handleProcessSale} onDeleteSale={handleDeleteSale} userRole={userRole} userName={userName} qrImage={qrImage} />;
      case 'staff':
        return <StaffView staff={staff} />;
      case 'reports':
        return <ReportsView events={events} sales={sales} onDeleteEvent={handleDeleteEvent} userRole={userRole} />;
      case 'others':
        return <OthersView qrImage={qrImage} onUpdateQrImage={updateQrImage} />;
      case 'settings':
        return <SettingsView />;
      default:
        return (
          <EventsView 
            events={events} 
            onDeleteEvent={handleDeleteEvent} 
            userRole={userRole} 
            userName={userName}
            eventTypes={eventTypes}
          />
        );
    }
  };

  const filteredMenuItems = MENU_ITEMS.filter(item => item.roles.includes(userRole));

  const getActiveTitle = () => {
    return MENU_ITEMS.find(item => item.id === currentView)?.label || 'Gran Cañaveral';
  };

  const handleLogout = async () => {
    await signOut(auth);
    setIsAuthenticated(false);
  };

  if (isInitializing) {
    return (
      <div className="min-h-screen bg-slate-900 flex items-center justify-center">
        <div className="flex flex-col items-center gap-4">
          <div className="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin" />
          <p className="text-white font-bold tracking-widest text-xs uppercase opacity-50">Cargando Sistema...</p>
        </div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return <LoginView onLogin={(name) => { setIsAuthenticated(true); setUserName(name); }} staff={staff} />;
  }

  return (
    <div className="min-h-screen bg-surface-base flex">
      <Sidebar 
        currentView={currentView} 
        onViewChange={setCurrentView} 
        items={filteredMenuItems} 
        userRole={userRole}
        onRoleChange={setUserRole}
        onLogout={handleLogout}
      />
      
      <main className="flex-1 ml-64 min-w-0">
        <Header title={getActiveTitle()} />
        <div className="p-8 max-w-7xl mx-auto w-full">
          {renderView()}
        </div>
      </main>
    </div>
  );
}

