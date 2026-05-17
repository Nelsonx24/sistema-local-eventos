import { useState, type FormEvent } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { 
  DollarSign, 
  Tag, 
  CreditCard, 
  Plus, 
  X, 
  ShoppingCart, 
  Calculator, 
  Wallet, 
  Calendar,
  ChevronRight,
  ClipboardList,
  Lock,
  Trash2,
  Maximize2,
  QrCode,
  Printer
} from 'lucide-react';
import { SaleRecord, InventoryItem, EventRecord, EventStatus, StaffRole } from '../types';
import { format } from 'date-fns';
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';

import { addDocument, updateDocument, deleteDocument } from '../services/firebaseService';

interface SalesViewProps {
  sales: SaleRecord[];
  inventory: InventoryItem[];
  events: EventRecord[];
  onProcessSale: (sale: SaleRecord) => void;
  onDeleteSale?: (id: string) => void;
  userRole: StaffRole;
  userName: string;
  qrImage: string;
}

export const SalesView = ({ sales, inventory, events, onProcessSale, onDeleteSale, userRole, userName, qrImage }: SalesViewProps) => {
  const isAdmin = userRole === StaffRole.ADMIN;
  const [activeEventId, setActiveEventId] = useState<string | null>(null);
  const [showModal, setShowModal] = useState(false);
  const [showCloseConfirm, setShowCloseConfirm] = useState(false);
  const [showMaximizedQR, setShowMaximizedQR] = useState(false);
  const [cashReceived, setCashReceived] = useState<string>('');
  const [newSale, setNewSale] = useState({
    clientName: '',
    paymentMethod: 'Efectivo' as any
  });
  
  const [cart, setCart] = useState<{name: string, quantity: number, type: 'Unidad' | 'Caja', subtotal: number}[]>([]);
  const [selectedItem, setSelectedItem] = useState('');
  const [selectedType, setSelectedType] = useState<'Unidad' | 'Caja'>('Caja');
  const [qty, setQty] = useState(1);

  const activeEvent = events.find(e => e.id === activeEventId);
  
  // Filter events that are NOT closed
  const activeEvents = events.filter(e => e.status !== EventStatus.CLOSED);

  const handleCloseEvent = async () => {
    if (!activeEventId || activeEventId === 'Venta Directa') return;

    await updateDocument('events', activeEventId, { status: EventStatus.CLOSED });
    setActiveEventId(null);
    setShowCloseConfirm(false);
  };

  const addToCart = () => {
    const item = inventory.find(i => i.name === selectedItem);
    if (!item) return;

    const price = selectedType === 'Caja' ? item.pricePerBox : item.pricePerUnit;
    const subtotal = price * qty;

    setCart([...cart, { name: item.name, quantity: qty, type: selectedType, subtotal }]);
    setSelectedItem('');
    setQty(1);
  };

  const cashNum = parseFloat(cashReceived) || 0;
  const totalAmount = cart.reduce((acc, curr) => acc + curr.subtotal, 0);
  const changeGiven = cashNum > totalAmount ? cashNum - totalAmount : 0;

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (cart.length === 0) return;

    const sale: SaleRecord = {
      id: `SL-${Math.floor(1000 + Math.random() * 9000).toString()}`,
      clientName: newSale.clientName || activeEvent?.clientName || 'Cliente General',
      eventId: activeEventId || 'Venta Directa',
      items: cart,
      amount: totalAmount,
      cashReceived: cashNum,
      changeGiven: changeGiven,
      date: format(new Date(), 'yyyy-MM-dd'),
      paymentMethod: newSale.paymentMethod,
      sellerName: userName,
      status: 'Paid',
      isPrinted: false
    };

    onProcessSale(sale);
    
    // Auto-print upon processing if desired (optional, but we'll focus on history printing as requested)
    
    setShowModal(false);
    setCart([]);
    setCashReceived('');
    setNewSale({ ...newSale, clientName: activeEvent?.clientName || '' });
  };

  // Only show sales for the active event
  const eventSales = activeEventId 
    ? sales.filter(s => s.eventId === activeEventId)
    : [];

  const sortedSales = [...eventSales].sort((a, b) => b.id.localeCompare(a.id));

  const handlePrintTicket = async (sale: SaleRecord) => {
    if (sale.isPrinted) return;

    const doc = new jsPDF({
      unit: 'mm',
      format: [80, 150] // Receipt printer size
    });

    // ... (rest of the PDF generation code)
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('SALÓN DE EVENTOS GRAN CAÑAVERAL', 40, 15, { align: 'center' });
    
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    doc.text('Ticket de Venta', 40, 22, { align: 'center' });
    doc.text(`ID: ${sale.id}`, 40, 26, { align: 'center' });
    doc.text(`Fecha: ${sale.date}`, 10, 32);
    doc.text(`Cliente: ${sale.clientName}`, 10, 36);
    doc.text(`Vendedor: ${sale.sellerName || 'Sistema'}`, 10, 40);
    doc.line(10, 42, 70, 42);

    // Items
    autoTable(doc, {
      startY: 44,
      margin: { left: 8, right: 8 },
      head: [['Cant', 'Item', 'Subtotal']],
      body: sale.items.map(i => [
        `${i.quantity} ${i.type.substring(0, 1)}`,
        i.name,
        `${i.subtotal} Bs`
      ]),
      theme: 'plain',
      styles: { fontSize: 7 },
      headStyles: { fontStyle: 'bold' }
    });

    // Summary
    const finalY = (doc as any).lastAutoTable.finalY + 5;
    doc.setFont('helvetica', 'bold');
    doc.text(`TOTAL: ${sale.amount} Bs`, 70, finalY, { align: 'right' });
    doc.setFont('helvetica', 'normal');
    doc.text(`Pago: ${sale.paymentMethod}`, 10, finalY + 5);
    
    if (sale.paymentMethod === 'Efectivo') {
      doc.text(`Efectivo: ${sale.cashReceived} Bs`, 10, finalY + 9);
      doc.text(`Cambio: ${sale.changeGiven} Bs`, 10, finalY + 13);
    }

    doc.line(10, finalY + 16, 70, finalY + 16);
    doc.setFontSize(6);
    doc.text('Gracias por su preferencia', 40, finalY + 22, { align: 'center' });

    doc.save(`ticket-${sale.id}.pdf`);

    // Mark as printed
    await updateDocument('sales', sale.id, { isPrinted: true });
  };

  // --- RENDERING VIEWS ---

  // 1. Selector View (Before choosing event)
  if (!activeEventId) {
    return (
      <motion.div 
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        className="flex flex-col gap-8 max-w-4xl mx-auto"
      >
        <div className="text-center space-y-2">
          <h2 className="text-3xl font-extrabold text-slate-900">Punto de Venta de Eventos</h2>
          <p className="text-slate-500">Seleccione un evento activo para comenzar a registrar consumos y ventas.</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {activeEvents.map(event => (
            <button
              key={event.id}
              onClick={() => {
                setActiveEventId(event.id);
                setNewSale(prev => ({ ...prev, clientName: event.clientName }));
              }}
              className="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-brand-accent transition-all text-left flex items-center justify-between"
            >
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent group-hover:bg-brand-accent group-hover:text-white transition-colors">
                  <Calendar size={24} />
                </div>
                <div>
                  <p className="font-bold text-slate-900 group-hover:text-brand-accent transition-colors">{event.clientName}</p>
                  <p className="text-xs text-slate-500">{event.eventType} • {event.date}</p>
                  <div className="mt-2 flex items-center gap-2">
                    <span className="text-[10px] font-bold px-2 py-0.5 bg-slate-100 rounded-full text-slate-600 uppercase tracking-wider">{event.id}</span>
                    <span className="text-[10px] font-bold px-2 py-0.5 bg-green-50 text-green-600 rounded-full uppercase tracking-wider">{event.status}</span>
                  </div>
                </div>
              </div>
              <ChevronRight size={20} className="text-slate-300 group-hover:text-brand-accent group-hover:translate-x-1 transition-all" />
            </button>
          ))}

          {isAdmin && (
            <button
              onClick={() => setActiveEventId('Venta Directa')}
              className="group bg-slate-50 p-6 rounded-2xl border border-dashed border-slate-300 hover:border-slate-800 hover:bg-slate-100 transition-all text-left flex items-center justify-between"
            >
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-slate-800 group-hover:text-white transition-colors">
                  <ShoppingCart size={24} />
                </div>
                <div>
                  <p className="font-bold text-slate-600 group-hover:text-slate-900">Venta Directa</p>
                  <p className="text-xs text-slate-400">Sin asociación a un evento específico</p>
                </div>
              </div>
              <ChevronRight size={20} className="text-slate-300 group-hover:text-slate-900 transition-all" />
            </button>
          )}
        </div>

        {activeEvents.length === 0 && (
          <div className="p-12 bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl text-center flex flex-col items-center gap-3">
             <ClipboardList size={48} className="text-slate-300" />
             <p className="text-slate-500 font-medium">No hay eventos activos para gestionar en este momento.</p>
          </div>
        )}
      </motion.div>
    );
  }

  // 2. POS View (After event selection)
  return (
    <motion.div 
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      className="flex flex-col gap-6"
    >
      {/* Event Header bar */}
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-6 py-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div className="flex items-center gap-4">
           <button 
             onClick={() => setActiveEventId(null)}
             className="p-2 hover:bg-slate-100 rounded-lg text-slate-400 transition-colors"
           >
             <X size={20} />
           </button>
           <div>
              <div className="flex items-center gap-2">
                <h3 className="font-bold text-lg text-slate-900">{activeEvent?.clientName || 'Venta Directa'}</h3>
                <span className="text-[10px] font-bold px-2 py-0.5 bg-blue-100 text-blue-700 rounded-md border border-blue-200">
                  ID: {activeEventId}
                </span>
              </div>
              <p className="text-xs text-slate-500 italic">Terminal de ventas activo para este evento</p>
           </div>
        </div>

        <div className="flex items-center gap-3 w-full md:w-auto">
          <button 
            disabled={showCloseConfirm}
            onClick={() => setShowModal(true)}
            className="flex-1 md:flex-none bg-brand-accent text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg shadow-blue-200 hover:shadow-xl hover:bg-blue-600 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed"
          >
            <Plus size={18} />
            Nueva Venta
          </button>
        </div>
      </div>

      {/* Sales History Table for this event */}
      <div className="flex flex-col gap-3">
        <div className="flex items-center justify-between px-2">
           <h4 className="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
              <ClipboardList size={14} />
              Historial de Ventas del Evento
           </h4>
           <span className="text-[0.75rem] font-bold text-brand-accent bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
             Total Acumulado: {eventSales.reduce((acc, s) => acc + s.amount, 0).toLocaleString()} Bs
           </span>
        </div>

        <div className="glass-card overflow-hidden">
          <div className="max-h-[500px] overflow-y-auto">
            <table className="w-full border-collapse text-left">
              <thead className="sticky top-0 z-10 bg-[#f8fafc] border-b border-border-subtle shadow-sm">
                <tr>
                  <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center w-24">ID</th>
                  <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Comprador</th>
                  <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Vendedor</th>
                  <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Detalle de Compra</th>
                  <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-right">Monto</th>
                  <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Pago</th>
                  <th className="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Acciones</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100 italic">
                {sortedSales.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="px-6 py-16 text-center text-slate-400">
                       <ShoppingCart size={40} className="mx-auto mb-4 opacity-10" />
                       <p className="text-sm font-medium">Aún no se han registrado ventas para este evento.</p>
                       <p className="text-xs">Usa el botón "Nueva Venta" para comenzar.</p>
                    </td>
                  </tr>
                ) : (
                  sortedSales.map((sale) => (
                    <tr key={sale.id} className="hover:bg-slate-50 transition-colors">
                      <td className="px-6 py-4 text-center">
                        <span className="text-[0.75rem] font-mono font-bold text-brand-accent">#{sale.id}</span>
                        <p className="text-[0.6rem] text-slate-400">{sale.date}</p>
                      </td>
                      <td className="px-6 py-4">
                        <p className="text-sm font-bold text-slate-800 leading-none">{sale.clientName}</p>
                      </td>
                      <td className="px-6 py-4 text-center">
                        <span className="text-[0.65rem] font-bold text-slate-400 uppercase">{sale.sellerName || 'Sistema'}</span>
                      </td>
                      <td className="px-6 py-4">
                        <div className="flex flex-wrap gap-2">
                          {sale.items.map((it, idx) => (
                            <span key={idx} className="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">
                              {it.quantity} {it.type}x {it.name}
                            </span>
                          ))}
                        </div>
                      </td>
                      <td className="px-6 py-4 text-right">
                        <p className="text-sm font-bold text-slate-900">{sale.amount.toLocaleString()} Bs</p>
                        {sale.changeGiven > 0 && <p className="text-[10px] text-emerald-600">Vuelto: {sale.changeGiven} Bs</p>}
                      </td>
                      <td className="px-6 py-4 text-center">
                        <div className="flex items-center justify-center gap-2">
                          <button
                            onClick={() => handlePrintTicket(sale)}
                            disabled={sale.isPrinted}
                            className={`p-2 rounded-lg transition-all ${
                              sale.isPrinted 
                                ? 'text-slate-200 cursor-not-allowed' 
                                : 'text-slate-400 hover:text-brand-accent hover:bg-blue-50'
                            }`}
                            title={sale.isPrinted ? "Ticket ya impreso" : "Imprimir Ticket"}
                          >
                            <Printer size={16} />
                          </button>
                          {isAdmin && (
                            <button 
                              onClick={() => onDeleteSale?.(sale.id)}
                              className="text-slate-300 hover:text-red-500 transition-colors p-2 hover:bg-red-50 rounded-lg"
                              title="Eliminar venta"
                            >
                              <Trash2 size={16} />
                            </button>
                          )}
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Finalize Event Section */}
      {activeEventId && activeEventId !== 'Venta Directa' && (
        <div className="mt-8 pt-8 border-t border-slate-200 flex flex-col items-center gap-4">
          <div className="text-center">
            <p className="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Zona de Seguridad</p>
            <p className="text-[0.65rem] text-slate-400">Una vez cerrado, no se podrán añadir más productos al inventario de este evento</p>
          </div>
          <button 
            type="button"
            onClick={() => setShowCloseConfirm(true)}
            className="group flex items-center gap-4 px-10 py-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 hover:bg-red-600 hover:text-white transition-all shadow-sm hover:shadow-xl active:scale-95"
          >
            <div className="w-10 h-10 bg-white text-red-600 rounded-xl flex items-center justify-center group-hover:bg-red-700 group-hover:text-white transition-colors shadow-sm">
              <Lock size={20} />
            </div>
            <div className="text-left">
              <p className="text-sm font-bold uppercase tracking-tight">Finalizar y Cerrar Evento</p>
              <p className="text-[10px] opacity-70">Cierra la terminal de ventas para: {activeEvent?.clientName}</p>
            </div>
          </button>
        </div>
      )}

      <AnimatePresence>
        {showCloseConfirm && (
          <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <motion.div 
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-white p-8 rounded-3xl shadow-2xl max-w-md w-full text-center flex flex-col gap-6 border border-red-100"
            >
              <div className="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-2">
                <Lock size={32} />
              </div>
              <div className="space-y-2">
                <h3 className="text-xl font-extrabold text-slate-900">¿Cerrar terminal de ventas?</h3>
                <p className="text-sm text-slate-500 underline decoration-red-200 underline-offset-4">Esta acción es irreversible y marcará el evento como terminado.</p>
              </div>
              <div className="flex gap-3">
                <button 
                  onClick={() => setShowCloseConfirm(false)}
                  className="flex-1 px-6 py-3 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 transition-all"
                >
                  Cancelar
                </button>
                <button 
                  onClick={() => {
                    handleCloseEvent();
                    setShowCloseConfirm(false);
                  }}
                  className="flex-1 px-6 py-3 rounded-xl bg-red-600 text-white font-bold shadow-lg shadow-red-200 hover:bg-red-700 transition-all"
                >
                  Sí, Cerrar Evento
                </button>
              </div>
            </motion.div>
          </div>
        )}
        {showModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div 
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              className="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden border border-border-subtle flex flex-col md:flex-row h-[600px]"
            >
              <div className="w-full md:w-1/2 p-6 border-r border-border-subtle flex flex-col gap-4 overflow-y-auto">
                <div className="flex flex-col gap-1 mb-2">
                  <h3 className="font-bold text-text-main flex items-center gap-2">
                    <ShoppingCart size={18} className="text-brand-accent" />
                    Nueva Venta
                  </h3>
                  <div className="px-2 py-1 bg-blue-50 text-blue-700 text-[0.65rem] font-bold uppercase rounded border border-blue-100 flex items-center gap-1.5">
                    <Tag size={10} />
                    {activeEvent ? `${activeEvent.id} | ${activeEvent.clientName}` : 'Venta Directa'}
                  </div>
                </div>
                
                <div className="flex flex-col gap-3">
                  <div className="flex flex-col gap-1">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Nombre del Comprador</label>
                    <input 
                      type="text" 
                      placeholder="Nombre..."
                      className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none focus:ring-1 focus:ring-brand-accent/30"
                      value={newSale.clientName}
                      onChange={e => setNewSale({...newSale, clientName: e.target.value})}
                    />
                  </div>

                  <div className="flex flex-col gap-1">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Tipo de Pago</label>
                    <div className="flex gap-2">
                      {['Efectivo', 'QR', 'Tarjeta'].map(method => (
                        <button
                          key={method}
                          onClick={() => setNewSale({...newSale, paymentMethod: method as any})}
                          className={`flex-1 py-2 rounded-lg text-[0.7rem] font-bold border transition-all ${newSale.paymentMethod === method ? 'bg-slate-800 text-white border-slate-800 shadow-sm' : 'bg-white text-text-muted border-border-subtle hover:bg-slate-50'}`}
                        >
                          {method}
                        </button>
                      ))}
                    </div>
                  </div>

                  {newSale.paymentMethod === 'QR' && (
                    <div className="animate-in fade-in zoom-in-95 duration-300">
                      <button 
                        type="button"
                        onClick={() => setShowMaximizedQR(true)}
                        className="w-full py-4 bg-indigo-50 text-indigo-600 rounded-2xl font-bold flex items-center justify-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-all shadow-sm"
                      >
                        <QrCode size={18} />
                        Mostrar QR de Pago
                      </button>
                      <p className="text-[9px] text-slate-400 text-center mt-2 italic font-medium uppercase tracking-tighter">Click para agrandar la imagen del QR</p>
                    </div>
                  )}

                  <div className="flex flex-col gap-1">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Agregar Producto</label>
                    <select 
                      className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none"
                      value={selectedItem}
                      onChange={e => setSelectedItem(e.target.value)}
                    >
                      <option value="">Seleccionar...</option>
                      {inventory.map(i => (
                        <option key={i.id} value={i.name}>{i.name} ({i.boxes} cajas / {i.looseUnits} uni)</option>
                      ))}
                    </select>
                  </div>
                </div>

                {selectedItem && (
                  <div className="bg-slate-50 p-3 rounded-lg border border-border-subtle flex flex-col gap-3">
                    <div className="flex gap-2">
                      <button 
                         onClick={() => setSelectedType('Caja')}
                         className={`flex-1 py-1.5 rounded-md text-xs font-bold transition-all ${selectedType === 'Caja' ? 'bg-brand-accent text-white shadow-sm' : 'bg-white text-text-muted border border-border-subtle'}`}
                      >
                        Caja
                      </button>
                      <button 
                        onClick={() => setSelectedType('Unidad')}
                        className={`flex-1 py-1.5 rounded-md text-xs font-bold transition-all ${selectedType === 'Unidad' ? 'bg-brand-accent text-white shadow-sm' : 'bg-white text-text-muted border border-border-subtle'}`}
                      >
                        Unidad
                      </button>
                    </div>
                    <div className="flex items-center gap-4">
                      <div className="flex-1 flex flex-col gap-1">
                        <label className="text-[0.6rem] font-bold text-text-muted uppercase">Cantidad</label>
                        <input 
                          type="number" 
                          min="1"
                          className="w-full px-3 py-1.5 bg-white border border-border-subtle rounded-md text-sm outline-none" 
                          value={isNaN(qty) ? '' : qty}
                          onChange={e => setQty(e.target.value === '' ? 1 : parseInt(e.target.value))}
                        />
                      </div>
                      <button 
                        onClick={addToCart}
                        className="mt-4 bg-slate-800 text-white p-2 rounded-md hover:bg-slate-700"
                      >
                        <Plus size={18} />
                      </button>
                    </div>
                  </div>
                )}
              </div>

              <div className="w-full md:w-1/2 bg-slate-50 flex flex-col h-full">
                <div className="p-4 border-b border-border-subtle flex justify-between items-center bg-white shadow-sm">
                  <span className="font-bold text-xs uppercase text-text-muted tracking-widest">Resumen y Cobro</span>
                  <button onClick={() => setShowModal(false)} className="hover:bg-slate-100 p-1 rounded transition-colors"><X size={18} /></button>
                </div>
                
                <div className="flex-1 p-4 overflow-y-auto flex flex-col gap-2">
                  {cart.length === 0 ? (
                    <div className="flex flex-col items-center justify-center h-full text-text-muted opacity-30">
                       <ShoppingCart size={40} strokeWidth={1.5} />
                       <p className="text-[0.65rem] font-bold mt-2 uppercase">Carrito vacío</p>
                    </div>
                  ) : (
                    cart.map((it, idx) => (
                      <motion.div 
                        initial={{ x: 20, opacity: 0 }}
                        animate={{ x: 0, opacity: 1 }}
                        key={idx} 
                        className="bg-white p-3 rounded-lg border border-border-subtle flex justify-between items-center shadow-sm"
                      >
                        <div>
                          <p className="text-[0.8rem] font-bold text-text-main leading-none">{it.name}</p>
                          <p className="text-[0.65rem] text-text-muted font-medium">{it.quantity} {it.type}(s)</p>
                        </div>
                        <div className="flex items-center gap-3">
                          <span className="text-[0.8rem] font-bold text-brand-accent">{it.subtotal.toLocaleString()} Bs</span>
                          <button 
                            onClick={() => setCart(cart.filter((_, i) => i !== idx))}
                            className="text-red-300 hover:text-red-500 transition-colors"
                          >
                            <X size={14} />
                          </button>
                        </div>
                      </motion.div>
                    ))
                  )}
                </div>

                <div className="p-6 bg-white border-t border-border-subtle mt-auto shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
                   <div className="flex flex-col gap-4 mb-4">
                      <div className="flex justify-between items-center">
                        <span className="text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Total a Pagar</span>
                        <span className="text-2xl font-bold text-brand-primary">{totalAmount.toLocaleString()} Bs</span>
                      </div>
                      
                      <div className={`flex flex-col gap-1 p-3 rounded-xl transition-colors ${newSale.paymentMethod === 'Efectivo' ? 'bg-emerald-50 border border-emerald-100' : 'bg-slate-50 border border-border-subtle'}`}>
                        <label className="text-[0.6rem] font-bold text-text-muted uppercase flex items-center justify-between">
                          <span>Monto Recibido ({newSale.paymentMethod === 'Efectivo' ? 'Efectivo' : 'Confirmado'})</span>
                          <Wallet size={12} className={newSale.paymentMethod === 'Efectivo' ? 'text-emerald-500' : 'text-slate-400'} />
                        </label>
                        <input 
                          disabled={newSale.paymentMethod !== 'Efectivo'}
                          type="number"
                          className="w-full bg-transparent text-xl font-bold text-text-main outline-none disabled:opacity-80"
                          value={newSale.paymentMethod === 'Efectivo' ? cashReceived : totalAmount}
                          onChange={e => setCashReceived(e.target.value)}
                        />
                      </div>

                      {newSale.paymentMethod === 'Efectivo' && (
                        <div className="flex justify-between items-center p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                          <span className="text-[0.65rem] font-bold text-blue-600 uppercase">Cambio sugerido</span>
                          <span className={`text-xl font-bold ${changeGiven >= 0 ? 'text-blue-700' : 'text-red-600'}`}>
                            {changeGiven.toLocaleString()} Bs
                          </span>
                        </div>
                      )}
                   </div>

                   <button 
                    disabled={cart.length === 0 || (newSale.paymentMethod === 'Efectivo' && cashNum < totalAmount)}
                    onClick={handleSubmit}
                    className="w-full bg-slate-900 text-white py-3.5 rounded-xl font-bold hover:bg-black transition-all shadow-lg active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed"
                   >
                     Confirmar Operación
                   </button>
                   {newSale.paymentMethod === 'Efectivo' && cashNum < totalAmount && cart.length > 0 && (
                     <p className="text-center text-[0.65rem] text-red-500 mt-3 font-bold animate-pulse uppercase tracking-wider">
                       Monto insuficiente en caja
                     </p>
                   )}
                </div>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      <AnimatePresence>
        {showMaximizedQR && (
          <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
            <motion.div 
              initial={{ scale: 0.8, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.8, opacity: 0 }}
              className="bg-white p-8 rounded-3xl shadow-2xl relative max-w-sm w-full flex flex-col items-center gap-6"
            >
              <button 
                onClick={() => setShowMaximizedQR(false)}
                className="absolute top-4 right-4 p-2 hover:bg-slate-100 rounded-full text-slate-400 transition-colors"
              >
                <X size={24} />
              </button>
              
              <div className="w-full aspect-square bg-white rounded-2xl overflow-hidden border border-slate-100 p-4">
                <img src={qrImage} alt="Payment QR Maximized" className="w-full h-full object-contain" />
              </div>
              
              <div className="text-center space-y-1">
                <h3 className="text-xl font-black text-slate-900 tracking-tight">Cobro por QR</h3>
                <p className="text-sm font-bold text-brand-accent">Monto Total: {totalAmount.toLocaleString()} Bs</p>
              </div>
              
              <button 
                onClick={() => setShowMaximizedQR(false)}
                className="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all"
              >
                Cerrar Imagen
              </button>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </motion.div>
  );
};
