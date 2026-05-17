import React, { useState, useEffect, useRef, type ChangeEvent } from 'react';
import { motion } from 'motion/react';
import { QrCode, Upload, Check, RefreshCw, Info, LayoutGrid, Plus, Trash2, Edit2, Save, X, FileDown, FileText, Bell, Smartphone, Send } from 'lucide-react';
import { subscribeToCollection, addDocument, updateDocument, deleteDocument } from '../services/firebaseService';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import { doc, getDoc, setDoc } from 'firebase/firestore';
import { db } from '../lib/firebase';

interface Asset {
  id: string;
  name: string;
  category: 'Cocina' | 'Cantina';
  quantity: number;
  condition: 'Bueno' | 'Regular' | 'Malo';
  lastMaintenance?: string;
}

interface OthersViewProps {
  qrImage: string;
  onUpdateQrImage: (url: string) => void;
}

export const OthersView = ({ qrImage, onUpdateQrImage }: OthersViewProps) => {
  const [activeSubView, setActiveSubView] = useState<'menu' | 'qr' | 'assets' | 'contractData' | 'notifications'>('menu');
  const [preview, setPreview] = useState(qrImage);
  const [isSuccess, setIsSuccess] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Asset State
  const [assets, setAssets] = useState<Asset[]>([]);
  const [isAddingAsset, setIsAddingAsset] = useState(false);
  const [editingAsset, setEditingAsset] = useState<string | null>(null);
  const [newAsset, setNewAsset] = useState<Omit<Asset, 'id'>>({
    name: '',
    category: 'Cocina',
    quantity: 1,
    condition: 'Bueno'
  });

  // Contract Data State
  const [contractData, setContractData] = useState({
    salonName: 'Salón de Eventos GRAN CAÑAVERAL',
    representative: 'CINTHIA FLORES CHOQUE',
    representativeCI: '____________________',
    city: 'Cochabamba'
  });
  const [isSavingContract, setIsSavingContract] = useState(false);
  const [contractSaved, setContractSaved] = useState(false);

  useEffect(() => {
    if (activeSubView === 'assets') {
      const unsub = subscribeToCollection('assets', (data) => setAssets(data as Asset[]));
      return () => unsub();
    }
    if (activeSubView === 'contractData') {
      const loadContractData = async () => {
        const docRef = doc(db, 'config', 'contractSettings');
        const docSnap = await getDoc(docRef);
        if (docSnap.exists()) {
          setContractData(prev => ({ ...prev, ...docSnap.data() }));
        }
      };
      loadContractData();
    }
  }, [activeSubView]);

  const handleSaveContractData = async () => {
    setIsSavingContract(true);
    try {
      await setDoc(doc(db, 'config', 'contractSettings'), contractData);
      setContractSaved(true);
      setTimeout(() => setContractSaved(false), 3000);
    } catch (error) {
      console.error("Error saving contract data:", error);
    } finally {
      setIsSavingContract(false);
    }
  };

  const handleAddAsset = async () => {
    if (!newAsset.name) return;
    await addDocument('assets', newAsset);
    setNewAsset({ name: '', category: 'Cocina', quantity: 1, condition: 'Bueno' });
    setIsAddingAsset(false);
  };

  const handleUpdateAsset = async (id: string, data: Partial<Asset>) => {
    await updateDocument('assets', id, data);
    setEditingAsset(null);
  };

  const handleDeleteAsset = async (id: string) => {
    if (window.confirm('¿Está seguro de eliminar este activo?')) {
      await deleteDocument('assets', id);
    }
  };

  const downloadAssetsPDF = () => {
    const doc = new jsPDF();
    const now = new Date().toLocaleString();

    doc.setFontSize(18);
    doc.text('Inventario de Activos - Gran Cañaveral', 14, 20);
    doc.setFontSize(10);
    doc.text(`Generado: ${now}`, 14, 28);

    // Cocina
    const cocinaAssets = assets.filter(a => a.category === 'Cocina');
    doc.setFontSize(14);
    doc.text('1. Área de Cocina', 14, 40);

    autoTable(doc, {
      startY: 45,
      head: [['Nombre', 'Cantidad']],
      body: cocinaAssets.map(a => [a.name, a.quantity]),
      headStyles: { fillColor: [45, 55, 72] }, // Slate-700
      styles: { fontSize: 10 },
      margin: { left: 14, right: 14 }
    });

    // Cantina
    const finalY = (doc as any).lastAutoTable.finalY + 15;
    const cantinaAssets = assets.filter(a => a.category === 'Cantina');
    doc.setFontSize(14);
    doc.text('2. Área de Cantina', 14, finalY);

    autoTable(doc, {
      startY: finalY + 5,
      head: [['Nombre', 'Cantidad']],
      body: cantinaAssets.map(a => [a.name, a.quantity]),
      headStyles: { fillColor: [45, 55, 72] },
      styles: { fontSize: 10 },
      margin: { left: 14, right: 14 }
    });

    doc.save(`Inventario_Activos_${new Date().toISOString().split('T')[0]}.pdf`);
  };

  const handleFileChange = (e: ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setPreview(reader.result as string);
        setIsSuccess(false);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSave = () => {
    onUpdateQrImage(preview);
    setIsSuccess(true);
    setTimeout(() => setIsSuccess(false), 3000);
  };

  if (activeSubView === 'menu') {
    return (
      <motion.div 
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        className="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <button 
          onClick={() => setActiveSubView('qr')}
          className="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-indigo-400 transition-all text-left flex flex-col gap-4 group"
        >
          <div className="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <QrCode size={28} />
          </div>
          <div>
            <h3 className="text-lg font-black text-slate-900 tracking-tight">Gestión de QR</h3>
            <p className="text-sm text-slate-500 mt-1">Configura la imagen del código QR para cobros rápidos.</p>
          </div>
        </button>

        <button 
          onClick={() => setActiveSubView('assets')}
          className="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all text-left flex flex-col gap-4 group"
        >
          <div className="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <LayoutGrid size={28} />
          </div>
          <div>
            <h3 className="text-lg font-black text-slate-900 tracking-tight">Activos de Salón</h3>
            <p className="text-sm text-slate-500 mt-1">Inventario de cocina y cantina. Registra activos fijos.</p>
          </div>
        </button>

        <button 
          onClick={() => setActiveSubView('contractData')}
          className="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-indigo-400 transition-all text-left flex flex-col gap-4 group"
        >
          <div className="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <FileText size={28} />
          </div>
          <div>
            <h3 className="text-lg font-black text-slate-900 tracking-tight">Datos del Contrato</h3>
            <p className="text-sm text-slate-500 mt-1">Configura los datos del salón y representante legal.</p>
          </div>
        </button>

        <button 
          onClick={() => setActiveSubView('notifications')}
          className="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-blue-400 transition-all text-left flex flex-col gap-4 group"
        >
          <div className="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <Bell size={28} />
          </div>
          <div>
            <h3 className="text-lg font-black text-slate-900 tracking-tight">Notificaciones Móviles</h3>
            <p className="text-sm text-slate-500 mt-1">Configura avisos en tiempo real para el CM y personal.</p>
          </div>
        </button>

        {/* Future options placeholders */}
        <div className="bg-slate-50/50 p-8 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center gap-2 text-center opacity-60">
           <div className="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-300">
             <Info size={24} />
           </div>
           <p className="text-sm font-bold text-slate-400 uppercase tracking-widest">Próximamente</p>
           <p className="text-xs text-slate-400">Nuevas configuraciones</p>
        </div>
      </motion.div>
    );
  }

  if (activeSubView === 'assets') {
    return (
      <motion.div 
        initial={{ opacity: 0, x: 20 }}
        animate={{ opacity: 1, x: 0 }}
        className="max-w-5xl mx-auto space-y-6"
      >
        <button 
          onClick={() => setActiveSubView('menu')}
          className="text-slate-500 hover:text-slate-900 font-bold text-sm flex items-center gap-2 transition-colors mb-4"
        >
          ← Volver al menú
        </button>

        <div className="flex items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            <div className="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
              <LayoutGrid size={24} />
            </div>
            <div>
              <h2 className="text-2xl font-black text-slate-900 tracking-tight">Inventario de Activos</h2>
              <p className="text-sm text-slate-500">Gestión de equipamiento de Cocina y Cantina</p>
            </div>
          </div>
          <div className="flex gap-3">
            <button 
              onClick={downloadAssetsPDF}
              disabled={assets.length === 0}
              className="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-2xl font-bold flex items-center gap-2 hover:bg-slate-50 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <FileDown size={20} className="text-slate-400 group-hover:text-slate-600" />
              Descargar PDF
            </button>
            <button 
              onClick={() => setIsAddingAsset(true)}
              className="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 hover:bg-black transition-all shadow-xl shadow-slate-200"
            >
              <Plus size={20} />
              Registrar Activo
            </button>
          </div>
        </div>

        {isAddingAsset && (
          <motion.div 
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            className="bg-white p-6 rounded-3xl border border-emerald-100 shadow-xl space-y-6 border-l-8 border-l-emerald-500"
          >
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div className="flex flex-col gap-1.5">
                <label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Nombre del Activo</label>
                <input 
                  type="text" 
                  value={newAsset.name}
                  onChange={e => setNewAsset({ ...newAsset, name: e.target.value })}
                  placeholder="Ej: Licuadora Industrial"
                  className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                />
              </div>
              <div className="flex flex-col gap-1.5">
                <label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Categoría</label>
                <select 
                  value={newAsset.category}
                  onChange={e => setNewAsset({ ...newAsset, category: e.target.value as any })}
                  className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none"
                >
                  <option value="Cocina">Cocina</option>
                  <option value="Cantina">Cantina</option>
                </select>
              </div>
              <div className="flex flex-col gap-1.5">
                <label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Cantidad</label>
                <input 
                  type="number" 
                  value={newAsset.quantity}
                  onChange={e => setNewAsset({ ...newAsset, quantity: parseInt(e.target.value) || 1 })}
                  className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none"
                />
              </div>
              <div className="flex flex-col gap-1.5">
                <label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Estado</label>
                <select 
                  value={newAsset.condition}
                  onChange={e => setNewAsset({ ...newAsset, condition: e.target.value as any })}
                  className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none"
                >
                  <option value="Bueno">Bueno</option>
                  <option value="Regular">Regular</option>
                  <option value="Malo">Malo</option>
                </select>
              </div>
            </div>
            <div className="flex justify-end gap-3 pt-2">
              <button onClick={() => setIsAddingAsset(false)} className="px-6 py-3 text-slate-500 font-bold text-sm">Cancelar</button>
              <button onClick={handleAddAsset} className="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all">Guardar Activo</button>
            </div>
          </motion.div>
        )}

        <div className="grid grid-cols-1 gap-4">
          {assets.length === 0 ? (
            <div className="p-20 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center gap-4">
              <div className="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-slate-300">
                <LayoutGrid size={32} />
              </div>
              <p className="text-slate-400 text-sm font-medium">No hay activos registrados. Comience registrando equipamiento de cocina o cantina.</p>
            </div>
          ) : (
            <div className="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
              <table className="w-full text-left">
                <thead className="bg-slate-50 border-b border-slate-200">
                  <tr>
                    <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Activo</th>
                    <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Categoría</th>
                    <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Cantidad</th>
                    <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado</th>
                    <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {assets.map(asset => (
                    <tr key={asset.id} className="hover:bg-slate-50 transition-colors group">
                      <td className="px-6 py-4 font-bold text-slate-700">{asset.name}</td>
                      <td className="px-6 py-4">
                        <span className={`px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider ${asset.category === 'Cocina' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'bg-blue-50 text-blue-600 border border-blue-100'}`}>
                          {asset.category}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-center font-mono font-bold text-slate-900">{asset.quantity}</td>
                      <td className="px-6 py-4">
                        <span className={`flex items-center gap-1.5 text-xs font-bold ${
                          asset.condition === 'Bueno' ? 'text-emerald-600' : 
                          asset.condition === 'Regular' ? 'text-amber-600' : 'text-red-600'
                        }`}>
                          <div className={`w-1.5 h-1.5 rounded-full ${
                             asset.condition === 'Bueno' ? 'bg-emerald-500' : 
                             asset.condition === 'Regular' ? 'bg-amber-500' : 'bg-red-500'
                          }`} />
                          {asset.condition}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-right">
                        <div className="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                          <button 
                            onClick={() => handleDeleteAsset(asset.id)}
                            className="p-2 text-slate-400 hover:text-red-500 transition-colors"
                          >
                            <Trash2 size={16} />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </motion.div>
    );
  }

  if (activeSubView === 'contractData') {
    return (
      <motion.div 
        initial={{ opacity: 0, x: 20 }}
        animate={{ opacity: 1, x: 0 }}
        className="max-w-3xl mx-auto space-y-6"
      >
        <button 
          onClick={() => setActiveSubView('menu')}
          className="text-slate-500 hover:text-slate-900 font-bold text-sm flex items-center gap-2 transition-colors mb-4"
        >
          ← Volver al menú
        </button>
        <div className="flex items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            <div className="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
              <FileText size={24} />
            </div>
            <div>
              <h2 className="text-2xl font-black text-slate-900 tracking-tight">Datos Generales del Contrato</h2>
              <p className="text-sm text-slate-500">Configura la información legal que aparecerá en los contratos</p>
            </div>
          </div>
          <button 
            onClick={handleSaveContractData}
            disabled={isSavingContract}
            className={`px-8 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all shadow-xl ${
              contractSaved 
                ? 'bg-emerald-500 text-white shadow-emerald-200' 
                : 'bg-indigo-600 text-white shadow-indigo-200 hover:bg-indigo-700'
            }`}
          >
            {isSavingContract ? <RefreshCw size={20} className="animate-spin" /> : contractSaved ? <Check size={20} /> : <Save size={20} />}
            {isSavingContract ? 'Guardando...' : contractSaved ? '¡Guardado!' : 'Guardar Cambios'}
          </button>
        </div>

        <div className="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="flex flex-col gap-2">
              <label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Nombre del Salón</label>
              <input 
                type="text" 
                value={contractData.salonName}
                onChange={e => setContractData({ ...contractData, salonName: e.target.value })}
                className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500"
              />
            </div>
            <div className="flex flex-col gap-2">
              <label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Representante Legal</label>
              <input 
                type="text" 
                value={contractData.representative}
                onChange={e => setContractData({ ...contractData, representative: e.target.value })}
                className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500"
              />
            </div>
            <div className="flex flex-col gap-2">
              <label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">CI del Representante</label>
              <input 
                type="text" 
                value={contractData.representativeCI}
                onChange={e => setContractData({ ...contractData, representativeCI: e.target.value })}
                className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500"
              />
            </div>
            <div className="flex flex-col gap-2">
              <label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Ciudad de Firma</label>
              <input 
                type="text" 
                value={contractData.city}
                onChange={e => setContractData({ ...contractData, city: e.target.value })}
                className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500"
              />
            </div>
          </div>
          
          <div className="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex gap-3 items-start">
            <Info className="text-indigo-500 shrink-0 mt-0.5" size={18} />
            <p className="text-xs text-slate-600 leading-relaxed font-medium">
              Estos datos se utilizarán automáticamente para rellenar los espacios correspondientes en el contrato del evento al generar el PDF.
            </p>
          </div>
        </div>
      </motion.div>
    );
  }

  if (activeSubView === 'notifications') {
    return (
      <motion.div 
        initial={{ opacity: 0, x: 20 }}
        animate={{ opacity: 1, x: 0 }}
        className="max-w-3xl mx-auto space-y-6"
      >
        <button 
          onClick={() => setActiveSubView('menu')}
          className="text-slate-500 hover:text-slate-900 font-bold text-sm flex items-center gap-2 transition-colors mb-4"
        >
          ← Volver al menú
        </button>

        <div className="flex items-center gap-4">
          <div className="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
            <Bell size={24} />
          </div>
          <div>
            <h2 className="text-2xl font-black text-slate-900 tracking-tight">Notificaciones de Eventos</h2>
            <p className="text-sm text-slate-500">Recibe avisos al instante sobre nuevos eventos y pagos</p>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-6 text-left">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                <Smartphone size={20} />
              </div>
              <h3 className="font-bold text-slate-900">Activar en Celular</h3>
            </div>
            <p className="text-xs text-slate-500 leading-relaxed">
              Para recibir notificaciones en su celular Android, abra este sistema en Chrome, vaya a opciones (⋮) y seleccione <b>"Instalar Aplicación"</b> o <b>"Añadir a pantalla de inicio"</b>.
            </p>
            <button 
              onClick={() => {
                const url = window.location.href;
                navigator.clipboard.writeText(url);
                alert('¡Link copiado! Envíelo a su celular para instalar la PWA (Aplicación Web Progresiva).');
              }}
              className="w-full bg-slate-100 text-slate-700 py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 hover:bg-slate-200 transition-colors"
            >
              Copiar Link para Celular
            </button>
          </div>

          <div className="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-6 text-left">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <Bell size={20} />
              </div>
              <h3 className="font-bold text-slate-900">Permisos del Navegador</h3>
            </div>
            <p className="text-xs text-slate-500 leading-relaxed">
              Habilite las notificaciones en este dispositivo para recibir alertas incluso con la pestaña cerrada.
            </p>
            <button 
              onClick={() => {
                if ('Notification' in window) {
                   Notification.requestPermission().then(permission => {
                     if (permission === 'granted') {
                       new Notification('¡Configurado!', { 
                         body: 'Las notificaciones del Salón Gran Cañaveral están activas.',
                       });
                     }
                   });
                }
              }}
              className="w-full bg-blue-600 text-white py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-blue-100 group"
            >
              Habilitar Notificaciones
              <Send size={14} className="group-hover:translate-x-1 transition-transform" />
            </button>
          </div>
        </div>

        <div className="bg-slate-900 p-8 rounded-[2.5rem] text-white relative overflow-hidden text-left">
           <div className="relative z-10 space-y-4">
              <div className="flex items-center gap-2 text-indigo-400">
                <Info size={16} />
                <h4 className="text-[10px] font-bold uppercase tracking-widest">Información para el CM</h4>
              </div>
              <p className="text-sm opacity-70 max-w-lg">
                El rol de <b>CM (Community Manager)</b> tiene acceso total de visualización al módulo de eventos para coordinar agendas y publicidad. Por seguridad, no puede modificar datos sensibles para evitar errores accidentales en la agenda oficial.
              </p>
              <div className="pt-4 flex gap-4">
                <div className="flex flex-col gap-1">
                  <span className="text-[10px] font-bold text-emerald-400">DISPONIBLE</span>
                  <span className="text-xs font-medium">Ver Agenda</span>
                </div>
                <div className="flex flex-col gap-1">
                  <span className="text-[10px] font-bold text-emerald-400">DISPONIBLE</span>
                  <span className="text-xs font-medium">Registrar Prospectos</span>
                </div>
                <div className="flex flex-col gap-1">
                  <span className="text-[10px] font-bold text-red-400">BLOQUEADO</span>
                  <span className="text-xs font-medium">Eliminar / Editar</span>
                </div>
              </div>
           </div>
           <Bell size={120} className="absolute -right-8 -bottom-8 opacity-5 -rotate-12" />
        </div>
      </motion.div>
    );
  }

  return (
    <motion.div 
      initial={{ opacity: 0, x: 20 }}
      animate={{ opacity: 1, x: 0 }}
      className="max-w-3xl mx-auto space-y-6"
    >
      <button 
        onClick={() => setActiveSubView('menu')}
        className="text-slate-500 hover:text-slate-900 font-bold text-sm flex items-center gap-2 transition-colors mb-4"
      >
        ← Volver al menú
      </button>

      <div className="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
        {/* ... existing QR management content ... */}
        <div className="flex items-center gap-4 mb-8">
          <div className="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
            <QrCode size={24} />
          </div>
          <div>
            <h2 className="text-xl font-black text-slate-900 tracking-tight">Gestión de Cobro QR</h2>
            <p className="text-sm text-slate-500">Actualice la imagen del código QR que se muestra a los clientes al pagar.</p>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
          <div className="space-y-6">
            <div className="p-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center gap-4 group hover:border-indigo-400 transition-all">
               <div className="w-full aspect-square bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden relative">
                  <img src={preview} alt="QR Preview" className="w-full h-full object-contain p-2" />
                  <div className="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <button 
                      onClick={() => fileInputRef.current?.click()}
                      className="bg-white text-slate-900 px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 shadow-xl"
                    >
                      <Upload size={14} />
                      Cambiar Imagen
                    </button>
                  </div>
               </div>
               <input 
                 type="file" 
                 ref={fileInputRef}
                 className="hidden" 
                 accept="image/*"
                 onChange={handleFileChange}
               />
               <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Vista previa del cobro</p>
            </div>
          </div>

          <div className="space-y-6">
            <div className="bg-indigo-50 border border-indigo-100 p-4 rounded-xl flex gap-3">
              <Info className="text-indigo-500 shrink-0" size={20} />
              <p className="text-xs text-indigo-700 leading-relaxed font-medium">
                Asegúrese de que el código QR sea legible y contenga los datos correctos de su cuenta bancaria para evitar contratiempos en los cobros.
              </p>
            </div>

            <div className="space-y-3">
               <button 
                onClick={handleSave}
                disabled={preview === qrImage}
                className={`w-full py-4 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg ${
                  isSuccess 
                    ? 'bg-emerald-500 text-white shadow-emerald-200' 
                    : 'bg-indigo-600 text-white shadow-indigo-200 hover:bg-indigo-700 disabled:opacity-50 disabled:shadow-none'
                }`}
               >
                 {isSuccess ? (
                   <>
                     <Check size={18} />
                     ¡QR Actualizado!
                   </>
                 ) : (
                   <>
                     <RefreshCw size={18} />
                     Guardar Cambios
                   </>
                 )}
               </button>
               
               {preview !== qrImage && !isSuccess && (
                 <button 
                  onClick={() => setPreview(qrImage)}
                  className="w-full py-4 rounded-2xl font-bold text-sm text-slate-500 hover:bg-slate-100 transition-all"
                 >
                   Descartar Cambios
                 </button>
               )}
            </div>
          </div>
        </div>
      </div>
      
      <div className="p-6 bg-slate-900 rounded-3xl text-white relative overflow-hidden">
        <div className="relative z-10">
          <h3 className="text-xs font-bold text-indigo-400 uppercase tracking-[0.2em] mb-4">Configuraciones Adicionales</h3>
          <p className="text-sm opacity-70 max-w-md">Próximamente podrá configurar otros parámetros del sistema como términos y condiciones, avisos legales y políticas de privacidad.</p>
        </div>
        <QrCode size={80} className="absolute -right-4 -bottom-4 text-white opacity-[0.05] rotate-12" />
      </div>
    </motion.div>
  );
};
