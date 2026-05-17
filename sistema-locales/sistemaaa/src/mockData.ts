import { EventRecord, EventType, EventStatus, InventoryItem, SaleRecord, StaffMember } from "./types";

export const SALON_STATS = {
  monthlyRevenue: 125400,
  eventsThisMonth: 18,
  inventoryAlerts: 3,
  staffCount: 24
};

export const MOCK_EVENTS: EventRecord[] = [
  { id: 'EV-001', clientName: 'Familia Rodriguez', clientId: '12345678', eventType: EventType.WEDDING, date: '2024-06-15', guests: 200, status: EventStatus.CONFIRMED, totalAmount: 45000, advancePayment: 15000, balancePending: 30000, paymentDueDate: '2024-06-01' },
  { id: 'EV-002', clientName: 'TechCorp S.A.', clientId: '98765432', eventType: EventType.CORPORATE, date: '2024-06-20', guests: 80, status: EventStatus.PENDING, totalAmount: 12000, advancePayment: 2000, balancePending: 10000, paymentDueDate: '2024-06-10' },
  { id: 'EV-003', clientName: 'Lucía Méndez', clientId: '45678901', eventType: EventType.BIRTHDAY, date: '2024-06-22', guests: 50, status: EventStatus.CONFIRMED, totalAmount: 5000, advancePayment: 5000, balancePending: 0, paymentDueDate: '2024-06-22' },
];

export const MOCK_INVENTORY: InventoryItem[] = [
  { id: 'INV-001', name: 'Refresco Cola 2L', category: 'Bebidas', boxes: 10, unitsPerBox: 6, looseUnits: 3, pricePerBox: 120, pricePerUnit: 25, status: 'In Stock' },
  { id: 'INV-002', name: 'Vino Tinto Reserva', category: 'Bebidas', boxes: 5, unitsPerBox: 12, looseUnits: 0, pricePerBox: 1800, pricePerUnit: 180, status: 'In Stock' },
  { id: 'INV-003', name: 'Cerveza Nacional 355ml', category: 'Bebidas', boxes: 20, unitsPerBox: 24, looseUnits: 15, pricePerBox: 380, pricePerUnit: 20, status: 'In Stock' },
  { id: 'INV-004', name: 'Servilletas Lujo', category: 'Consumibles', boxes: 15, unitsPerBox: 50, looseUnits: 20, pricePerBox: 250, pricePerUnit: 6, status: 'In Stock' },
];

export const MOCK_SALES: SaleRecord[] = [
  { 
    id: 'SL-5521', 
    eventId: 'EV-001', 
    clientName: 'Familia Rodriguez', 
    items: [{ name: 'Refresco Cola 2L', quantity: 2, type: 'Caja', subtotal: 240 }],
    amount: 240, 
    cashReceived: 240,
    changeGiven: 0,
    date: '2024-05-10', 
    paymentMethod: 'Transferencia', 
    status: 'Partial' 
  },
  { 
    id: 'SL-5522', 
    eventId: 'EV-003', 
    clientName: 'Lucía Méndez', 
    items: [{ name: 'Vino Tinto Reserva', quantity: 3, type: 'Unidad', subtotal: 540 }],
    amount: 540, 
    cashReceived: 600,
    changeGiven: 60,
    date: '2024-05-12', 
    paymentMethod: 'Tarjeta', 
    status: 'Paid' 
  },
];

export const MOCK_STAFF: StaffMember[] = [
  { id: 'ST-01', name: 'Carlos Ruiz', role: 'Administrador', username: 'admin', password: 'admin123', email: 'carlos@salon.com', status: 'Active', avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Carlos' },
  { id: 'ST-02', name: 'Ana Belén', role: 'Catering', username: 'ana', password: '123', email: 'ana@salon.com', status: 'Active', avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Ana' },
  { id: 'ST-03', name: 'Vendedor Demo', role: 'Vendedor', username: 'vendedor', password: 'vendedor123', email: 'vendedor@salon.com', status: 'Active', avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Vendedor' },
];
