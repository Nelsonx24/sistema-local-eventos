import { useState, type FormEvent } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Package, Trash2, Edit2, AlertTriangle, Check, Layers, Disc, X, Plus, DollarSign } from 'lucide-react';
import { InventoryItem } from '../types';

import { StaffRole } from '../types';

import { addDocument, updateDocument, deleteDocument } from '../services/firebaseService';

interface InventoryViewProps {
  inventory: InventoryItem[];
  onDeleteInventory?: (id: string) => void;
  userRole: StaffRole;
}

export const InventoryView = ({ inventory, onDeleteInventory, userRole }: InventoryViewProps) => {
  const isAdmin = userRole === StaffRole.ADMIN;
  const [activeModal, setActiveModal] = useState<'register' | 'prices' | 'stock' | 'audit' | null>(null);
  const [selectedProductId, setSelectedProductId] = useState<string | null>(null);
  
  const [newItem, setNewItem] = useState({
    name: '',
    category: 'Bebidas' as any,
    boxes: 0,
    unitsPerBox: 1,
    looseUnits: 0,
    pricePerBox: 0,
    pricePerUnit: 0
  });

  const [stockUpdate, setStockUpdate] = useState({
    boxesToAdd: 0,
    looseToAdd: 0
  });

  const [auditStock, setAuditStock] = useState({
    physicalBoxes: 0,
    physicalLoose: 0
  });

  const [priceUpdate, setPriceUpdate] = useState({
    pricePerBox: 0,
    pricePerUnit: 0
  });

  const handleAuditStock = async (e: FormEvent) => {
    e.preventDefault();
    if (!selectedProductId) return;

    const item = inventory.find(i => i.id === selectedProductId);
    if (item) {
      const { id, ...data } = item;
      await updateDocument('inventory', id, {
        ...data,
        boxes: auditStock.physicalBoxes,
        looseUnits: auditStock.physicalLoose
      });
    }
    setActiveModal(null);
    setSelectedProductId(null);
  };

  const handleAddProduct = async (e: FormEvent) => {
    e.preventDefault();
    const product = {
      ...newItem,
      status: 'In Stock'
    };
    await addDocument('inventory', product);
    setActiveModal(null);
  };

  const handleUpdateStock = async (e: FormEvent) => {
    e.preventDefault();
    const item = inventory.find(i => i.id === selectedProductId);
    if (item) {
      const { id, ...data } = item;
      await updateDocument('inventory', id, {
        ...data,
        boxes: item.boxes + stockUpdate.boxesToAdd,
        looseUnits: item.looseUnits + stockUpdate.looseToAdd
      });
    }
    setActiveModal(null);
    setStockUpdate({ boxesToAdd: 0, looseToAdd: 0 });
  };

  const handleUpdatePrices = async (e: FormEvent) => {
    e.preventDefault();
    const item = inventory.find(i => i.id === selectedProductId);
    if (item) {
      const { id, ...data } = item;
      await updateDocument('inventory', id, {
        ...data,
        pricePerBox: priceUpdate.pricePerBox,
        pricePerUnit: priceUpdate.pricePerUnit
      });
    }
    setActiveModal(null);
  };

  const openPriceModal = (item: InventoryItem) => {
    setSelectedProductId(item.id);
    setPriceUpdate({ pricePerBox: item.pricePerBox, pricePerUnit: item.pricePerUnit });
    setActiveModal('prices');
  };

  const openStockModal = (item: InventoryItem) => {
    setSelectedProductId(item.id);
    setActiveModal('stock');
  };

  return (
    <motion.div 
      initial={{ opacity: 0, x: 12 }}
      animate={{ opacity: 1, x: 0 }}
      className="flex flex-col gap-6"
    >
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-6 py-4 bg-white rounded-lg border border-border-subtle shadow-sm">
        <h3 className="font-semibold text-text-main">Administración de Inventario</h3>
        <div className="flex flex-wrap gap-2">
          <button 
            onClick={() => setActiveModal('register')}
            className="bg-slate-800 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-slate-700 flex items-center gap-2 shadow-sm transition-all"
          >
            <Plus size={14} />
            Registrar Nuevo Producto
          </button>
          <button 
            onClick={() => setActiveModal('prices')}
            className="bg-blue-600 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-blue-500 flex items-center gap-2 shadow-sm transition-all"
          >
            <DollarSign size={14} />
            Actualizar Precios
          </button>
          <button 
            onClick={() => setActiveModal('stock')}
            className="bg-emerald-600 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-emerald-500 flex items-center gap-2 shadow-sm transition-all"
          >
            <Layers size={14} />
            Reabastecer Stock
          </button>
          <button 
            onClick={() => {
              setSelectedProductId(null);
              setAuditStock({ physicalBoxes: 0, physicalLoose: 0 });
              setActiveModal('audit');
            }}
            className="bg-purple-600 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-purple-500 flex items-center gap-2 shadow-sm transition-all"
          >
            <Check size={14} />
            Cotejo Físico
          </button>
        </div>
      </div>

      <div className="glass-card overflow-hidden">
        <table className="w-full border-collapse text-left">
          <thead>
            <tr className="bg-[#f8fafc] border-b border-border-subtle">
              <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest w-[30%]">Producto</th>
              <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Cajas</th>
              <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Unidades</th>
              <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Precios (Caja/Uni)</th>
              <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Estado</th>
              <th className="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody className="divide-y divide-[#f1f5f9]">
            {inventory.map((item) => (
              <tr key={item.id} className="hover:bg-[#f8fafc] transition-colors">
                <td className="px-6 py-4">
                  <div className="flex items-center gap-3">
                    <div className="p-2 bg-slate-100 rounded-lg text-slate-500">
                      <Package size={18} />
                    </div>
                    <div>
                      <p className="text-[0.875rem] font-bold text-text-main leading-none mb-1">{item.name}</p>
                      <p className="text-[0.7rem] text-text-muted">{item.category} • {item.unitsPerBox} uni/caja</p>
                    </div>
                  </div>
                </td>
                <td className="px-6 py-4 text-center">
                   <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 rounded-full font-mono font-bold text-[0.875rem]">
                     <Layers size={14} />
                     {item.boxes}
                   </div>
                </td>
                <td className="px-6 py-4 text-center">
                   <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 rounded-full font-mono font-bold text-[0.875rem]">
                     <Disc size={14} />
                     {item.looseUnits}
                   </div>
                </td>
                <td className="px-6 py-4">
                  <div className="flex flex-col">
                    <span className="text-[0.8rem] font-bold text-text-main">${item.pricePerBox} <small className="text-text-muted font-normal">/caja</small></span>
                    <span className="text-[0.8rem] font-bold text-brand-accent">${item.pricePerUnit} <small className="text-text-muted font-normal">/unidad</small></span>
                  </div>
                </td>
                <td className="px-6 py-4">
                   <span className={`status-pill inline-flex items-center gap-1.5 ${
                    item.status === 'In Stock' && item.boxes > 2 ? 'bg-emerald-50 text-emerald-700' :
                    item.boxes <= 2 ? 'bg-amber-50 text-amber-700' :
                    'bg-slate-100 text-slate-700'
                  }`}>
                    {item.boxes <= 2 ? <AlertTriangle size={12} /> : <Check size={12} />}
                    {item.boxes <= 2 ? 'Stock Bajo' : 'Disponible'}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <div className="flex justify-end gap-2 text-text-muted">
                    {isAdmin && (
                      <>
                        <button className="p-1 hover:text-brand-accent transition-colors"><Edit2 size={16} /></button>
                        <button 
                          onClick={() => onDeleteInventory?.(item.id)}
                          className="p-1 hover:text-red-500 transition-colors"
                        >
                          <Trash2 size={16} />
                        </button>
                      </>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <AnimatePresence>
        {activeModal === 'register' && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div initial={{ scale: 0.95 }} animate={{ scale: 1 }} className="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
              <div className="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
                <h3 className="font-bold text-text-main flex items-center gap-2"><Plus size={18} /> Registrar Nuevo Producto</h3>
                <button onClick={() => setActiveModal(null)}><X size={20} /></button>
              </div>
              <form onSubmit={handleAddProduct} className="p-6 flex flex-col gap-4">
                <div className="flex flex-col gap-1.5 text-left">
                  <label className="text-[0.65rem] font-bold text-text-muted uppercase">Nombre del Producto</label>
                  <input required className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" value={newItem.name} onChange={e => setNewItem({...newItem, name: e.target.value})} />
                </div>
                <div className="grid grid-cols-2 gap-4 text-left">
                   <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase">Categoría</label>
                    <select className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" value={newItem.category} onChange={e => setNewItem({...newItem, category: e.target.value as any})}>
                      <option value="Bebidas">Bebidas</option>
                      <option value="Consumibles">Consumibles</option>
                    </select>
                  </div>
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase">Uni x Caja</label>
                    <input 
                      required 
                      type="number" 
                      className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" 
                      value={isNaN(newItem.unitsPerBox) ? '' : newItem.unitsPerBox} 
                      onChange={e => setNewItem({...newItem, unitsPerBox: e.target.value === '' ? 0 : parseInt(e.target.value)})} 
                    />
                  </div>
                </div>
                <button type="submit" className="mt-4 bg-slate-800 text-white py-2.5 rounded-lg font-bold">Crear Ficha de Producto</button>
              </form>
            </motion.div>
          </div>
        )}

        {activeModal === 'stock' && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div initial={{ scale: 0.95 }} animate={{ scale: 1 }} className="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
              <div className="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-emerald-50">
                <h3 className="font-bold text-emerald-800 flex items-center gap-2"><Layers size={18} /> Reabastecer Stock</h3>
                <button onClick={() => setActiveModal(null)}><X size={20} /></button>
              </div>
              <form onSubmit={handleUpdateStock} className="p-6 flex flex-col gap-4 text-left">
                <div className="flex flex-col gap-1.5">
                  <label className="text-[0.65rem] font-bold text-text-muted uppercase">Seleccionar Producto</label>
                  <select required className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" value={selectedProductId || ''} onChange={e => setSelectedProductId(e.target.value)}>
                    <option value="">Elegir producto...</option>
                    {inventory.map(i => <option key={i.id} value={i.id}>{i.name} (Actual: {i.boxes} cajas / {i.looseUnits} uni)</option>)}
                  </select>
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase">Cajas a Añadir</label>
                    <input 
                      type="number" 
                      className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" 
                      value={isNaN(stockUpdate.boxesToAdd) ? '' : stockUpdate.boxesToAdd} 
                      onChange={e => setStockUpdate({...stockUpdate, boxesToAdd: e.target.value === '' ? 0 : parseInt(e.target.value)})} 
                    />
                  </div>
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase">Uni a Añadir</label>
                    <input 
                      type="number" 
                      className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" 
                      value={isNaN(stockUpdate.looseToAdd) ? '' : stockUpdate.looseToAdd} 
                      onChange={e => setStockUpdate({...stockUpdate, looseToAdd: e.target.value === '' ? 0 : parseInt(e.target.value)})} 
                    />
                  </div>
                </div>
                <button type="submit" className="mt-4 bg-emerald-600 text-white py-2.5 rounded-lg font-bold">Sumar al Inventario</button>
              </form>
            </motion.div>
          </div>
        )}

        {activeModal === 'prices' && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div initial={{ scale: 0.95 }} animate={{ scale: 1 }} className="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
              <div className="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-blue-50">
                <h3 className="font-bold text-blue-800 flex items-center gap-2"><DollarSign size={18} /> Actualizar Precios</h3>
                <button onClick={() => setActiveModal(null)}><X size={20} /></button>
              </div>
              <form onSubmit={handleUpdatePrices} className="p-6 flex flex-col gap-4 text-left">
                <div className="flex flex-col gap-1.5">
                  <label className="text-[0.65rem] font-bold text-text-muted uppercase">Producto</label>
                  <select required className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" value={selectedProductId || ''} onChange={e => {
                    const item = inventory.find(i => i.id === e.target.value);
                    setSelectedProductId(e.target.value);
                    if (item) setPriceUpdate({ pricePerBox: item.pricePerBox, pricePerUnit: item.pricePerUnit });
                  }}>
                    <option value="">Elegir producto...</option>
                    {inventory.map(i => <option key={i.id} value={i.id}>{i.name}</option>)}
                  </select>
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-wider">Precio Caja ($)</label>
                    <input 
                      required 
                      type="number" 
                      className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" 
                      value={isNaN(priceUpdate.pricePerBox) ? '' : priceUpdate.pricePerBox} 
                      onChange={e => setPriceUpdate({...priceUpdate, pricePerBox: e.target.value === '' ? 0 : parseInt(e.target.value)})} 
                    />
                  </div>
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-wider">Precio Unidad ($)</label>
                    <input 
                      required 
                      type="number" 
                      className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" 
                      value={isNaN(priceUpdate.pricePerUnit) ? '' : priceUpdate.pricePerUnit} 
                      onChange={e => setPriceUpdate({...priceUpdate, pricePerUnit: e.target.value === '' ? 0 : parseInt(e.target.value)})} 
                    />
                  </div>
                </div>
                <button type="submit" className="mt-4 bg-blue-600 text-white py-2.5 rounded-lg font-bold">Publicar Nuevos Precios</button>
              </form>
            </motion.div>
          </div>
        )}

        {activeModal === 'audit' && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div initial={{ scale: 0.95 }} animate={{ scale: 1 }} className="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
              <div className="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-purple-50">
                <h3 className="font-bold text-purple-800 flex items-center gap-2"><Check size={18} /> Cotejo de Inventario Físico</h3>
                <button onClick={() => setActiveModal(null)}><X size={20} /></button>
              </div>
              <form onSubmit={handleAuditStock} className="p-6 flex flex-col gap-4 text-left">
                <div className="p-3 bg-purple-50 border border-purple-100 rounded-lg text-[0.7rem] text-purple-700 font-medium">
                  Ingrese las cantidades reales encontradas físicamente en el almacén. El sistema se actualizará para coincidir con estas cifras.
                </div>
                
                <div className="flex flex-col gap-1.5">
                  <label className="text-[0.65rem] font-bold text-text-muted uppercase">Producto a Verificar</label>
                  <select 
                    required 
                    className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" 
                    value={selectedProductId || ''} 
                    onChange={e => {
                      const item = inventory.find(i => i.id === e.target.value);
                      setSelectedProductId(e.target.value);
                      if (item) setAuditStock({ physicalBoxes: item.boxes, physicalLoose: item.looseUnits });
                    }}
                  >
                    <option value="">Elegir producto...</option>
                    {inventory.map(i => <option key={i.id} value={i.id}>{i.name}</option>)}
                  </select>
                </div>

                {selectedProductId && (
                  <>
                    <div className="grid grid-cols-2 gap-4">
                      <div className="flex flex-col gap-1.5">
                        <label className="text-[0.65rem] font-bold text-text-muted uppercase">Cajas Físicas</label>
                        <input 
                          type="number" 
                          className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" 
                          value={isNaN(auditStock.physicalBoxes) ? '' : auditStock.physicalBoxes} 
                          onChange={e => setAuditStock({...auditStock, physicalBoxes: e.target.value === '' ? 0 : parseInt(e.target.value)})} 
                        />
                      </div>
                      <div className="flex flex-col gap-1.5">
                        <label className="text-[0.65rem] font-bold text-text-muted uppercase">Unidades Sueltas Físicas</label>
                        <input 
                          type="number" 
                          className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" 
                          value={isNaN(auditStock.physicalLoose) ? '' : auditStock.physicalLoose} 
                          onChange={e => setAuditStock({...auditStock, physicalLoose: e.target.value === '' ? 0 : parseInt(e.target.value)})} 
                        />
                      </div>
                    </div>

                    {(() => {
                      const item = inventory.find(i => i.id === selectedProductId);
                      if (!item) return null;
                      const diffBoxes = auditStock.physicalBoxes - item.boxes;
                      const diffLoose = auditStock.physicalLoose - item.looseUnits;
                      
                      return (
                        <div className="mt-2 p-3 bg-slate-50 rounded-lg border border-slate-200">
                          <p className="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Discrepancia detectada</p>
                          <div className="grid grid-cols-2 gap-2 text-xs">
                            <div className="flex justify-between">
                              <span className="text-slate-500">Cajas:</span>
                              <span className={diffBoxes === 0 ? 'text-slate-400' : diffBoxes > 0 ? 'text-emerald-600 font-bold' : 'text-red-600 font-bold'}>
                                {diffBoxes > 0 ? '+' : ''}{diffBoxes}
                              </span>
                            </div>
                            <div className="flex justify-between">
                              <span className="text-slate-500">Unidades:</span>
                              <span className={diffLoose === 0 ? 'text-slate-400' : diffLoose > 0 ? 'text-emerald-600 font-bold' : 'text-red-600 font-bold'}>
                                {diffLoose > 0 ? '+' : ''}{diffLoose}
                              </span>
                            </div>
                          </div>
                        </div>
                      );
                    })()}
                  </>
                )}
                
                <button 
                  type="submit" 
                  disabled={!selectedProductId}
                  className="mt-4 bg-purple-600 text-white py-2.5 rounded-lg font-bold shadow-lg shadow-purple-100 hover:bg-purple-700 transition-all disabled:opacity-50"
                >
                  Sincronizar Stock Físico
                </button>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </motion.div>
  );
};
