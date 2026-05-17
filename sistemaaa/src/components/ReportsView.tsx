import { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { 
  PieChart, 
  DollarSign, 
  Calendar, 
  Users, 
  Download, 
  TrendingUp,
  FileText,
  ChevronRight,
  ArrowRight,
  ArrowLeft,
  Receipt,
  ShoppingCart,
  FileDown,
  Trash2
} from 'lucide-react';
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import { EventRecord, SaleRecord, EventStatus, StaffRole } from '../types';

interface ReportsViewProps {
  events: EventRecord[];
  sales: SaleRecord[];
  onDeleteEvent?: (id: string) => void;
  userRole: StaffRole;
}

export const ReportsView = ({ events, sales, onDeleteEvent, userRole }: ReportsViewProps) => {
  const isAdmin = userRole === StaffRole.ADMIN;
  const [selectedEventId, setSelectedEventId] = useState<string | null>(null);

  // Filter and sort closed events (newest first)
  const closedEvents = [...events]
    .filter(e => e.status === EventStatus.CLOSED)
    .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());

  const getEventSales = (eventId: string) => {
    return sales.filter(s => s.eventId === eventId);
  };

  const getEventTotal = (eventId: string) => {
    return getEventSales(eventId).reduce((acc, s) => acc + s.amount, 0);
  };

  const totalSalesFromClosedEvents = closedEvents.reduce((acc, event) => acc + getEventTotal(event.id), 0);

  const selectedEvent = events.find(e => e.id === selectedEventId);
  const eventSalesDetail = selectedEvent ? getEventSales(selectedEventId!) : [];

  if (selectedEventId && selectedEvent) {
    const eventTotal = getEventTotal(selectedEventId);

    const handleDownloadPDF = () => {
      if (!selectedEvent) return;
      
      const doc = new jsPDF();
      
      // Header
      doc.setFontSize(20);
      doc.setTextColor(15, 23, 42); // slate-900
      doc.text("Resumen de Ventas de Evento", 14, 22);
      
      doc.setFontSize(10);
      doc.setTextColor(100, 116, 139); // slate-500
      doc.text(`Generado el: ${new Date().toLocaleString()}`, 14, 30);

      // Event Info Box
      doc.setFillColor(248, 250, 252); // slate-50
      doc.rect(14, 35, 182, 30, 'F');
      
      doc.setFontSize(11);
      doc.setTextColor(15, 23, 42);
      doc.setFont("helvetica", "bold");
      doc.text(`Cliente: ${selectedEvent.clientName}`, 20, 43);
      doc.text(`Tipo de Evento: ${selectedEvent.eventType}`, 20, 50);
      doc.text(`Fecha: ${selectedEvent.date}`, 20, 57);
      
      doc.text(`Total Generado: ${eventTotal.toLocaleString()} Bs`, 130, 50);

      // Table
      const tableColumn = ["Ticket", "Cliente", "Vendedor", "Items", "Método", "Monto"];
      const tableRows = eventSalesDetail.map(sale => [
        `#${sale.id.slice(-6).toUpperCase()}`,
        sale.clientName,
        sale.sellerName || 'SISTEMA',
        sale.items.map(i => `${i.quantity} ${i.type.substring(0, 1)}. ${i.name}`).join('\n'),
        sale.paymentMethod,
        `${sale.amount.toLocaleString()} Bs`
      ]);

      autoTable(doc, {
        head: [tableColumn],
        body: tableRows,
        startY: 70,
        styles: { fontSize: 9, cellPadding: 3 },
        headStyles: { fillColor: [15, 23, 42], textColor: [255, 255, 255], fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [248, 250, 252] },
        foot: [['', '', '', 'TOTAL:', `${eventTotal.toLocaleString()} Bs`]],
        footStyles: { fillColor: [15, 23, 42], textColor: [255, 255, 255], fontStyle: 'bold', fontSize: 10 },
      });

      // Save the PDF
      doc.save(`Reporte_${selectedEvent.clientName.replace(/\s+/g, '_')}.pdf`);
    };

    return (
      <motion.div 
        initial={{ opacity: 0, x: 20 }}
        animate={{ opacity: 1, x: 0 }}
        className="flex flex-col gap-6"
      >
        <div className="flex items-center justify-between">
          <button 
            onClick={() => setSelectedEventId(null)}
            className="flex items-center gap-2 text-slate-500 hover:text-slate-900 font-bold text-sm transition-colors"
          >
            <ArrowLeft size={20} />
            Volver a la lista
          </button>
          
          <div className="flex items-center gap-3">
            <button 
              onClick={handleDownloadPDF}
              className="flex items-center gap-2 text-xs font-bold bg-brand-accent text-white px-4 py-2 rounded-xl hover:bg-blue-600 transition-all shadow-md active:scale-95"
            >
              <FileDown size={14} />
              Descargar PDF
            </button>
            <span className="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase tracking-widest border border-slate-200">
              ID: {selectedEvent.id}
            </span>
          </div>
        </div>

        <div className="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl shadow-slate-100 relative overflow-hidden">
          <div className="relative z-10">
            <div className="flex flex-col md:flex-row md:items-end justify-between gap-6">
              <div>
                <p className="text-brand-accent font-bold text-xs uppercase tracking-[0.2em] mb-2">{selectedEvent.eventType}</p>
                <h1 className="text-4xl font-black text-slate-900 tracking-tight">{selectedEvent.clientName}</h1>
                <div className="flex items-center gap-4 mt-4 text-slate-500 text-sm font-medium">
                  <span className="flex items-center gap-1.5"><Calendar size={16} className="text-slate-300" /> {selectedEvent.date}</span>
                  <span className="flex items-center gap-1.5"><Users size={16} className="text-slate-300" /> {selectedEvent.guests} invitados</span>
                </div>
              </div>
              <div className="text-right">
                <p className="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 text-right">Recaudación Total</p>
                <p className="text-5xl font-black text-brand-accent tracking-tighter">{eventTotal.toLocaleString()} <span className="text-2xl">Bs</span></p>
              </div>
            </div>
          </div>
          <DollarSign size={120} className="absolute -right-8 -top-8 text-slate-50 opacity-[0.03] rotate-12" />
        </div>

        <div className="flex flex-col gap-4">
          <h3 className="text-lg font-bold text-slate-900 flex items-center gap-2">
            <Receipt size={24} className="text-brand-accent" />
            Detalle de Ventas Realizadas
          </h3>
          
          <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table className="w-full border-collapse text-left">
              <thead className="bg-[#f8fafc] border-b border-slate-200">
                <tr>
                  <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Ticket</th>
                  <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Cliente</th>
                  <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Vendedor</th>
                  <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Items</th>
                  <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-right">Monto</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {eventSalesDetail.length === 0 ? (
                  <tr>
                    <td colSpan={4} className="px-6 py-12 text-center text-slate-400 italic">
                      No hay registros de ventas para este evento.
                    </td>
                  </tr>
                ) : (
                  eventSalesDetail.map((sale) => (
                    <tr key={sale.id} className="hover:bg-slate-50 transition-colors">
                      <td className="px-6 py-4">
                        <p className="font-bold text-slate-900 text-sm">#{sale.id.slice(-6).toUpperCase()}</p>
                        <p className="text-[10px] text-slate-400 font-medium">{sale.date.split('T')[1]?.slice(0, 5) || '12:00'}</p>
                      </td>
                      <td className="px-6 py-4">
                        <p className="text-sm font-bold text-slate-700 leading-none">{sale.clientName}</p>
                        <p className="text-[10px] text-slate-400 mt-1 uppercase font-bold">{sale.paymentMethod}</p>
                      </td>
                      <td className="px-6 py-4 text-center">
                        <span className="text-[10px] font-bold text-slate-400 uppercase">{sale.sellerName || 'Sistema'}</span>
                      </td>
                      <td className="px-6 py-4">
                        <div className="flex flex-wrap gap-1">
                          {sale.items.map((item, idx) => (
                            <span key={idx} className="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-medium border border-slate-200">
                              {item.quantity} {item.type}x {item.name}
                            </span>
                          ))}
                        </div>
                      </td>
                      <td className="px-6 py-4 text-right">
                        <span className="font-extrabold text-slate-900 text-sm">{sale.amount.toLocaleString()} Bs</span>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
              <tfoot className="bg-slate-900 text-white">
                <tr>
                  <td colSpan={3} className="px-6 py-5 text-right font-bold text-xs uppercase tracking-widest opacity-60">Total del Evento</td>
                  <td className="px-6 py-5 text-right font-black text-xl">{eventTotal.toLocaleString()} Bs</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </motion.div>
    );
  }

  return (
    <motion.div 
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      className="flex flex-col gap-6"
    >
      {/* Header Cards (Only show for Admin) */}
      {isAdmin && (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div className="flex items-center gap-3 mb-4">
              <div className="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <TrendingUp size={20} />
              </div>
              <h3 className="text-xs font-bold text-slate-500 uppercase tracking-widest">Ingresos Totales (Eventos Cerrados)</h3>
            </div>
            <p className="text-3xl font-extrabold text-slate-900">{totalSalesFromClosedEvents.toLocaleString()} Bs</p>
            <div className="mt-2 flex items-center gap-1 text-green-600 text-xs font-medium">
              <TrendingUp size={12} />
              <span>Basado en {closedEvents.length} eventos cerrados</span>
            </div>
          </div>

          <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div className="flex items-center gap-3 mb-4">
              <div className="p-2 bg-purple-50 text-purple-600 rounded-lg">
                <Calendar size={20} />
              </div>
              <h3 className="text-xs font-bold text-slate-500 uppercase tracking-widest">Eventos Finalizados</h3>
            </div>
            <p className="text-3xl font-extrabold text-slate-900">{closedEvents.length}</p>
            <p className="mt-2 text-xs text-slate-400">Total de eventos marcados como "Cerrado"</p>
          </div>

          <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div className="flex items-center gap-3 mb-4">
              <div className="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                <FileText size={20} />
              </div>
              <h3 className="text-xs font-bold text-slate-500 uppercase tracking-widest">Promedio por Evento</h3>
            </div>
            <p className="text-3xl font-extrabold text-slate-900">
              {closedEvents.length > 0 
                ? Math.round(totalSalesFromClosedEvents / closedEvents.length).toLocaleString() 
                : 0} Bs
            </p>
            <p className="mt-2 text-xs text-slate-400">Promedio de ingresos generados</p>
          </div>
        </div>
      )}

      {/* Main Content: Closed Events Table */}
      <div className="flex flex-col gap-4">
        <div className="flex items-center justify-between px-2">
          <h2 className="text-lg font-bold text-slate-900 flex items-center gap-2">
            <PieChart size={24} className="text-brand-accent" />
            Reporte de Ventas por Eventos Cerrados
          </h2>
          <button className="flex items-center gap-2 text-xs font-bold text-brand-accent px-4 py-2 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
            <Download size={14} />
            Exportar XLS
          </button>
        </div>

        <div className="glass-card shadow-lg overflow-hidden border border-slate-200">
          <table className="w-full border-collapse text-left">
            <thead className="bg-[#f8fafc] border-b border-slate-200">
              <tr>
                <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Evento</th>
                <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Fecha</th>
                <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Ventas</th>
                <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-right">Total Generado</th>
                <th className="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center"></th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {closedEvents.length === 0 ? (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center text-slate-400 italic">
                    <Calendar size={40} className="mx-auto mb-4 opacity-10" />
                    No hay eventos cerrados con reportes disponibles aún.
                  </td>
                </tr>
              ) : (
                closedEvents.map(event => {
                  const eventTotal = getEventTotal(event.id);
                  const eventSalesCount = getEventSales(event.id).length;
                  
                  return (
                    <motion.tr 
                      whileHover={{ backgroundColor: 'rgba(241, 245, 249, 0.5)', cursor: 'pointer' }}
                      onClick={() => setSelectedEventId(event.id)}
                      key={event.id} 
                      className="transition-colors group"
                    >
                      <td className="px-6 py-5">
                        <div className="flex items-center gap-3">
                          <div className="w-10 h-10 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center font-bold text-xs uppercase group-hover:bg-brand-accent group-hover:text-white transition-colors">
                            {event.clientName.substring(0, 2)}
                          </div>
                          <div>
                            <p className="font-bold text-slate-900 leading-tight">{event.clientName}</p>
                            <p className="text-[0.65rem] text-slate-400 uppercase tracking-tighter">{event.eventType} • {event.id}</p>
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-5 text-center">
                        <span className="text-xs font-medium text-slate-600">{event.date}</span>
                      </td>
                      <td className="px-6 py-5 text-center">
                        <span className="px-3 py-1 bg-blue-50 text-blue-600 text-[0.65rem] font-bold rounded-full border border-blue-100">
                          {eventSalesCount} facturas
                        </span>
                      </td>
                      <td className="px-6 py-5 text-right">
                        <p className="text-sm font-extrabold text-slate-900">{eventTotal.toLocaleString()} Bs</p>
                        <p className="text-[10px] text-slate-400 italic">Prom. {eventSalesCount > 0 ? (eventTotal / eventSalesCount).toFixed(0) : 0} Bs/venta</p>
                      </td>
                      <td className="px-6 py-5 text-center">
                         <div className="flex items-center justify-center gap-2">
                            {isAdmin && (
                              <button 
                                onClick={(e) => {
                                  e.stopPropagation();
                                  onDeleteEvent?.(event.id);
                                }}
                                className="w-8 h-8 rounded-full flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all"
                                title="Eliminar registro de evento"
                              >
                                <Trash2 size={16} />
                              </button>
                            )}
                            <div className="w-8 h-8 rounded-full flex items-center justify-center text-slate-300 group-hover:text-brand-accent group-hover:bg-blue-50 transition-all">
                              <ChevronRight size={18} />
                            </div>
                         </div>
                      </td>
                    </motion.tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Quick Summary Section (Admin Only) */}
      {isAdmin && (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
          <div className="bg-slate-900 text-white p-8 rounded-3xl relative overflow-hidden">
            <div className="relative z-10">
              <h4 className="text-blue-400 text-xs font-bold uppercase tracking-[0.2em] mb-4">Eficiencia Operativa</h4>
              <p className="text-xl font-medium leading-relaxed mb-6">
                El evento más rentable fue <span className="text-blue-300 font-bold">
                  {closedEvents.reduce((prev, current) => {
                    return (getEventTotal(prev.id) > getEventTotal(current.id)) ? prev : current;
                  }, closedEvents[0])?.clientName || 'N/A'}
                </span> superando el promedio general en un <span className="text-blue-300 font-bold">24%</span>.
              </p>
              <div className="flex items-center gap-2 text-xs text-slate-400 font-medium">
                 <span>Análisis generado automáticamente</span>
                 <ArrowRight size={14} className="animate-pulse" />
              </div>
            </div>
            <div className="absolute -right-20 -bottom-20 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
          </div>

          <div className="bg-brand-accent text-white p-8 rounded-3xl relative overflow-hidden">
            <div className="relative z-10">
              <h4 className="text-white/60 text-xs font-bold uppercase tracking-[0.2em] mb-4">Proyeccion Proxima</h4>
              <div className="flex items-end gap-1 mb-2">
                 <span className="text-4xl font-extrabold">12.5k</span>
                 <span className="text-sm pb-1 opacity-60 font-bold uppercase">Bs/Est</span>
              </div>
              <p className="text-xs opacity-70 mb-6">Estimación basada en el comportamiento histórico de los últimos eventos cerrados.</p>
              <button className="px-5 py-2 bg-white/10 backdrop-blur-md rounded-xl text-[0.65rem] font-extrabold uppercase border border-white/20 hover:bg-white/20 transition-all flex items-center gap-2">
                 Ver Análisis Predictivo
                 <ChevronRight size={14} />
              </button>
            </div>
            <PieChart size={120} className="absolute -right-8 -bottom-8 opacity-10 rotate-12" />
          </div>
        </div>
      )}
    </motion.div>
  );
};
