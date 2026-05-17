import { useState, type FormEvent, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { 
  Plus, 
  Calendar as CalendarIcon, 
  Users as UsersIcon, 
  ChevronLeft, 
  ChevronRight, 
  FileText, 
  List, 
  X, 
  Download, 
  Trash2,
  FileUp,
  Eye,
  CheckCircle,
  AlertCircle,
  Info,
  DollarSign
} from 'lucide-react';
import { MOCK_EVENTS } from '../mockData';
import { EventStatus, EventRecord, StaffRole } from '../types';
import { 
  format, 
  addMonths, 
  subMonths, 
  startOfMonth, 
  endOfMonth, 
  startOfWeek, 
  endOfWeek, 
  isSameMonth, 
  isSameDay, 
  addDays, 
  eachDayOfInterval,
  parseISO
} from 'date-fns';
import { es } from 'date-fns/locale';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

import { addDocument, updateDocument, deleteDocument } from '../services/firebaseService';

interface EventsViewProps {
  events: EventRecord[];
  onDeleteEvent?: (id: string) => void;
  userRole: StaffRole;
  userName: string;
  eventTypes: string[];
}

export const EventsView = ({ 
  events, 
  onDeleteEvent, 
  userRole,
  userName,
  eventTypes
}: EventsViewProps) => {
  const isAdmin = userRole === StaffRole.ADMIN;
  const isCM = userRole === StaffRole.CM;
  const canModify = isAdmin || userRole === StaffRole.SELLER;
  const canDelete = isAdmin;
  const [viewMode, setViewMode] = useState<'list' | 'calendar'>('calendar');
  const [currentMonth, setCurrentMonth] = useState(new Date());
  const [showModal, setShowModal] = useState(false);
  const [showTypesModal, setShowTypesModal] = useState(false);
  const [selectedEventDetails, setSelectedEventDetails] = useState<EventRecord | null>(null);
  const [eventToUpload, setEventToUpload] = useState<EventRecord | null>(null);
  const [isConfirmingPay, setIsConfirmingPay] = useState(false);
  const [newType, setNewType] = useState('');
  const [contractSettings, setContractSettings] = useState({
    salonName: 'Salón de Eventos GRAN CAÑAVERAL',
    representative: 'CINTHIA FLORES CHOQUE',
    representativeCI: '____________________',
    city: 'Cochabamba'
  });

  const [newEvent, setNewEvent] = useState({
    clientName: '',
    clientId: '',
    eventType: eventTypes[0] || 'Social',
    date: format(new Date(), 'yyyy-MM-dd'),
    guests: 100,
    totalAmount: 0,
    advancePayment: 0,
    paymentDueDate: format(addDays(new Date(), 7), 'yyyy-MM-dd')
  });

  useEffect(() => {
    const loadContractSettings = async () => {
      try {
        const { doc, getDoc } = await import('firebase/firestore');
        const { db } = await import('../lib/firebase');
        const docRef = doc(db, 'config', 'contractSettings');
        const docSnap = await getDoc(docRef);
        if (docSnap.exists()) {
          setContractSettings(prev => ({ ...prev, ...docSnap.data() }));
        }
      } catch (error) {
        console.error("Error loading contract settings:", error);
      }
    };
    loadContractSettings();
  }, []);

  const generatePDF = (event: EventRecord) => {
    const doc = new jsPDF();
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const margin = 20;
    const contentWidth = pageWidth - (margin * 2);
    
    // Header
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('CONTRATO DE PRESTACIÓN DE SERVICIOS', pageWidth / 2, 20, { align: 'center' });
    doc.text(`“${contractSettings.salonName.toUpperCase()}”`, pageWidth / 2, 28, { align: 'center' });
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    
    const template = `Entre las partes:
Por una parte, {{CLIENTE}}, con documento de identidad N° {{CI_CLIENTE}}, en adelante denominado/a "EL CLIENTE".
Y por otra parte, {{REPRESENTANTE}}, representante legal del {{SALON}}, en adelante denominado "EL PROVEEDOR".
Ambas partes acuerdan celebrar el presente contrato de prestación de servicios, el cual se regirá por las siguientes cláusulas:

PRIMERA: OBJETO DEL CONTRATO
EL PROVEEDOR se compromete a brindar el servicio de alquiler del salón de eventos, incluyendo el uso de sus instalaciones para la realización del evento detallado por EL CLIENTE.

SEGUNDA: DESCRIPCIÓN DEL SERVICIO
El servicio incluye:
•	Uso del salón principal
•	Uso de mesas y sillas disponibles
•	Iluminación básica del ambiente
•	Baños y áreas comunes
•	Uso de cocina y cantina (bajo responsabilidad del cliente o personal autorizado)

TERCERA: FECHA, DURACIÓN Y HORARIO
El evento se realizará el día {{FECHA_EVENTO}}.
Horario establecido:
•	Inicio: 08:00 a.m.
•	Finalización: 11:59 p.m.
En caso de eventos de más de un día, el uso del salón se extenderá desde las 08:00 a.m. hasta las 06:00 p.m. del día siguiente, previa coordinación.

CUARTA: PRECIO Y FORMA DE PAGO
El costo total del servicio es de Bs. {{MONTO_TOTAL}}.
Forma de pago:
•	Anticipo: {{ADELANTO}} al momento de la firma del contrato
•	Saldo restante: deberá ser cancelado hasta {{FECHA_LIMITE}}
El incumplimiento de pago dentro del plazo establecido podrá generar la suspensión del servicio sin derecho a reclamo.

QUINTA: RESPONSABILIDADES DEL CLIENTE
EL CLIENTE se compromete a:
•	Hacer buen uso de las instalaciones
•	Responder por daños ocasionados por los asistentes
•	Respetar los horarios establecidos
•	Cumplir las normas de convivencia y seguridad del salón
•	Mantener el orden de los invitados

SEXTA: CANCELACIONES
En caso de cancelación por parte del CLIENTE:
•	Con más de 30 días de anticipación: devolución del 50% del anticipo
•	Con menos de 30 días: no habrá devolución del anticipo
En caso de fuerza mayor debidamente justificada, ambas partes podrán renegociar la fecha del evento.

SÉPTIMA: DAÑOS, PÉRDIDAS Y MULTAS
Cualquier daño ocasionado a las instalaciones, mobiliario o equipos será responsabilidad del CLIENTE, quien deberá cubrir los costos de reparación o reposición.

OCTAVA: ACEPTACIÓN
Ambas partes declaran haber leído y aceptado todas las cláusulas del presente contrato, firmando en señal de conformidad.

Firmado en la ciudad de {{CIUDAD}}, el día {{HOY}}`;

    // Replace placeholders
    const replacements: Record<string, string> = {
      '{{CLIENTE}}': event.clientName,
      '{{CI_CLIENTE}}': event.clientId || '____________________',
      '{{SALON}}': contractSettings.salonName,
      '{{REPRESENTANTE}}': contractSettings.representative,
      '{{CIUDAD}}': contractSettings.city,
      '{{FECHA_EVENTO}}': event.date,
      '{{MONTO_TOTAL}}': event.totalAmount.toLocaleString(),
      '{{ADELANTO}}': event.advancePayment.toLocaleString(),
      '{{SALDO}}': event.balancePending.toLocaleString(),
      '{{FECHA_LIMITE}}': event.paymentDueDate,
      '{{HOY}}': format(new Date(), 'dd/MM/yyyy')
    };

    let fullText = template;
    Object.entries(replacements).forEach(([key, value]) => {
      fullText = fullText.replace(new RegExp(key, 'g'), value);
    });

    // Use a smaller line height to make the contract more compact
    const lineHeight = 6.5;
    const splitText = doc.splitTextToSize(fullText, contentWidth);
    
    let currentY = 45;
    
    // Manual page handling to ensure it looks good
    splitText.forEach((line: string) => {
      if (currentY > pageHeight - margin - 15) {
        doc.addPage();
        currentY = margin + 15;
      }
      doc.text(line, margin, currentY);
      currentY += lineHeight;
    });

    // Signatures Area - ensure it starts on a new page or has enough space
    if (currentY > pageHeight - 80) {
      doc.addPage();
      currentY = 40;
    } else {
      currentY += 15; // Extra spacing before signatures
    }
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.line(40, currentY + 30, 90, currentY + 30);
    doc.text('EL CLIENTE', 65, currentY + 35, { align: 'center' });
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.text(event.clientName, 65, currentY + 42, { align: 'center' });
    doc.text(`CI: ${event.clientId || '__________'}`, 65, currentY + 48, { align: 'center' });
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.line(120, currentY + 30, 170, currentY + 30);
    doc.text('EL PROVEEDOR', 145, currentY + 35, { align: 'center' });
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.text(contractSettings.representative, 145, currentY + 42, { align: 'center' });
    doc.text(`CI: ${contractSettings.representativeCI}`, 145, currentY + 48, { align: 'center' });


    doc.save(`Contrato_${event.clientName.replace(/\s/g, '_')}.pdf`);
  };

  const generateCalendarPDF = () => {
    const doc = new jsPDF();
    
    // Header
    doc.setFontSize(20);
    doc.text('CALENDARIO DE EVENTOS', 105, 20, { align: 'center' });
    
    doc.setFontSize(10);
    doc.text(`Salón de Eventos Gran Cañaveral - Reporte de Calendario`, 105, 28, { align: 'center' });
    doc.text(`Generado el: ${format(new Date(), 'dd/MM/yyyy HH:mm')}`, 105, 33, { align: 'center' });
    doc.line(20, 36, 190, 36);

    // Sort events by date
    const sortedEvents = [...events].sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());

    autoTable(doc, {
      startY: 45,
      head: [['Fecha', 'Cliente', 'Tipo', 'Vendedor', 'Pax', 'Total', 'Saldo', 'Estado']],
      body: sortedEvents.map(event => [
        event.date,
        event.clientName,
        event.eventType,
        event.sellerName || 'N/A',
        event.guests,
        `$${event.totalAmount.toLocaleString()}`,
        `$${event.balancePending.toLocaleString()}`,
        event.status
      ]),
      theme: 'grid',
      headStyles: { fillColor: [15, 23, 42] },
      styles: { fontSize: 8, cellPadding: 3 },
      columnStyles: {
        5: { fontStyle: 'bold', textColor: [220, 38, 38] } // Saldo in red-ish
      }
    });

    doc.save(`Calendario_Eventos_${format(new Date(), 'yyyy-MM-dd')}.pdf`);
  };

  const handleCreateEvent = async (e: FormEvent) => {
    e.preventDefault();
    const createdEvent = {
      ...newEvent,
      sellerName: userName,
      balancePending: newEvent.totalAmount - newEvent.advancePayment,
      status: EventStatus.PENDING
    };
    await addDocument('events', createdEvent);
    setShowModal(false);
    setNewEvent({
      clientName: '',
      clientId: '',
      eventType: eventTypes[0] || 'Social',
      date: format(new Date(), 'yyyy-MM-dd'),
      guests: 100,
      totalAmount: 0,
      advancePayment: 0,
      paymentDueDate: format(addDays(new Date(), 7), 'yyyy-MM-dd')
    });
  };

  const handleAddEventType = async (e: FormEvent) => {
    e.preventDefault();
    if (!newType.trim()) return;
    if (eventTypes.includes(newType.trim())) return;
    const updatedTypes = [...eventTypes, newType.trim()];
    await updateDocument('config', 'eventTypes', { types: updatedTypes });
    setNewType('');
  };

  const handleDeleteEventType = async (type: string) => {
    const updatedTypes = eventTypes.filter(t => t !== type);
    await updateDocument('config', 'eventTypes', { types: updatedTypes });
  };

  const handleUploadContract = async (eventId: string) => {
    await updateDocument('events', eventId, { signedContractUrl: 'simulated-storage/contract.pdf' });
    setEventToUpload(null);
  };

  const handlePayBalance = async (eventId: string) => {
    const event = events.find(e => e.id === eventId);
    if (!event) return;
    
    await updateDocument('events', eventId, { 
      advancePayment: event.totalAmount, 
      balancePending: 0, 
      status: EventStatus.CONFIRMED 
    });
    
    if (selectedEventDetails?.id === eventId) {
      setSelectedEventDetails(prev => prev ? { ...prev, advancePayment: prev.totalAmount, balancePending: 0, status: EventStatus.CONFIRMED } : null);
    }
    setIsConfirmingPay(false);
  };

  const getEventTypeColor = (type: string) => {
    const hash = type.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
    const colors = [
      'bg-blue-50 text-blue-700 border-blue-100',
      'bg-purple-50 text-purple-700 border-purple-100',
      'bg-emerald-50 text-emerald-700 border-emerald-100',
      'bg-amber-50 text-amber-700 border-amber-100',
      'bg-rose-50 text-rose-700 border-rose-100',
      'bg-indigo-50 text-indigo-700 border-indigo-100'
    ];
    return colors[hash % colors.length];
  };

  const renderCalendar = () => {
    const monthStart = startOfMonth(currentMonth);
    const monthEnd = endOfMonth(monthStart);
    const startDate = startOfWeek(monthStart);
    const endDate = endOfWeek(monthEnd);
    const days = eachDayOfInterval({ start: startDate, end: endDate });

    return (
      <div className="grid grid-cols-7 border-t border-l border-border-subtle bg-white">
        {['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'].map(day => (
          <div key={day} className="px-4 py-2 border-r border-b border-border-subtle bg-slate-50 text-[10px] font-bold text-text-muted uppercase text-center">
            {day}
          </div>
        ))}
        {days.map(day => {
          const dayEvents = events.filter(e => isSameDay(parseISO(e.date), day));
          return (
            <div 
              key={day.toString()} 
              className={`min-h-[140px] p-2 border-r border-b border-border-subtle transition-all hover:bg-slate-50/80 ${
                !isSameMonth(day, monthStart) ? 'bg-slate-50/40 opacity-40' : 'bg-white'
              }`}
            >
              <div className="flex justify-between items-start mb-2">
                <span className={`text-[0.7rem] font-bold ${
                  isSameDay(day, new Date()) 
                    ? 'bg-slate-900 text-white w-6 h-6 flex items-center justify-center rounded-full shadow-sm' 
                    : 'text-slate-400'
                }`}>
                  {format(day, 'd')}
                </span>
                {dayEvents.length > 0 && isSameMonth(day, monthStart) && (
                  <span className="text-[10px] font-bold text-brand-accent bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">
                    {dayEvents.length}
                  </span>
                )}
              </div>

              <div className="flex flex-col gap-2 overflow-hidden h-[110px]">
                {dayEvents.map(event => (
                  <button
                    key={event.id}
                    onClick={() => setSelectedEventDetails(event)}
                    className={`text-[12px] text-left p-3 rounded-xl border shadow-md transition-all flex flex-col gap-1 h-full min-h-[50px] relative group overflow-hidden ${getEventTypeColor(event.eventType)} hover:scale-[1.03] active:scale-[0.97]`}
                  >
                    <div className="flex items-center justify-between relative z-10">
                      <span className="font-extrabold truncate max-w-[85%] uppercase tracking-tight leading-tight text-[0.75rem]">{event.clientName}</span>
                      {event.signedContractUrl && <CheckCircle size={12} className="text-emerald-500 shrink-0" />}
                    </div>
                    <div className="flex items-center justify-between text-[10px] opacity-90 relative z-10 mt-0.5">
                       <span className="font-bold tracking-tight truncate">{event.eventType}</span>
                       <span className={`font-black tracking-tighter ${event.balancePending === 0 ? 'text-emerald-600' : 'text-red-500'}`}>
                         {event.balancePending === 0 ? 'SALDADO' : `$${event.balancePending.toLocaleString()}`}
                       </span>
                    </div>
                    {/* Decorative background accent */}
                    <div className="absolute -right-2 -bottom-2 opacity-10 group-hover:opacity-20 transition-opacity">
                       <CalendarIcon size={36} />
                    </div>
                  </button>
                ))}
              </div>
            </div>
          );
        })}
      </div>
    );
  };

  return (
    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} className="flex flex-col gap-6">
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-6 py-4 bg-white rounded-lg border border-border-subtle shadow-sm">
        <div className="flex items-center gap-4">
          {(isAdmin || isCM) && (
            <div className="flex border border-border-subtle rounded-md overflow-hidden bg-slate-50">
              <button 
                onClick={() => setViewMode('calendar')}
                className={`p-2 ${viewMode === 'calendar' ? 'bg-white shadow-sm font-bold text-brand-accent' : 'text-text-muted'}`}
              >
                <CalendarIcon size={18} />
              </button>
              <button 
                onClick={() => setViewMode('list')}
                className={`p-2 ${viewMode === 'list' ? 'bg-white shadow-sm font-bold text-brand-accent' : 'text-text-muted'}`}
              >
                <List size={18} />
              </button>
            </div>
          )}
          
          {(isAdmin || viewMode === 'calendar') && viewMode === 'calendar' && (
            <div className="flex items-center gap-3">
              <button onClick={() => setCurrentMonth(subMonths(currentMonth, 1))} className="p-1 hover:bg-slate-100 rounded">
                <ChevronLeft size={18} />
              </button>
              <h3 className="text-sm font-bold uppercase tracking-wider w-32 text-center">
                {format(currentMonth, 'MMMM yyyy', { locale: es })}
              </h3>
              <button onClick={() => setCurrentMonth(addMonths(currentMonth, 1))} className="p-1 hover:bg-slate-100 rounded">
                <ChevronRight size={18} />
              </button>
            </div>
          )}
        </div>

        <div className="flex flex-wrap gap-2">
          {isAdmin && (
            <button 
              onClick={() => setShowTypesModal(true)}
              className="bg-slate-100 text-slate-700 px-4 py-2 rounded-[6px] flex items-center gap-2 text-[0.8rem] font-bold hover:bg-slate-200 transition-all border border-slate-200 shadow-sm"
            >
              <List size={16} />
              Gestionar Tipos
            </button>
          )}
          {isAdmin && (
            <button 
              onClick={generateCalendarPDF}
              className="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-[6px] flex items-center gap-2 text-[0.8rem] font-bold hover:bg-emerald-100 transition-all border border-emerald-200 shadow-sm"
            >
              <Download size={16} />
              Exportar Calendario PDF
            </button>
          )}
          <button 
            onClick={() => setShowModal(true)}
            className="bg-brand-primary text-white px-4 py-2 rounded-[6px] flex items-center gap-2 text-[0.8rem] font-bold hover:bg-slate-800 transition-all shadow-md active:scale-95"
          >
            <Plus size={16} />
            Registrar Nuevo Evento
          </button>
        </div>
      </div>

      <div className="glass-card overflow-hidden">
        {viewMode === 'list' ? (
          <table className="w-full border-collapse text-left">
            <thead>
              <tr className="bg-[#f8fafc] border-b border-border-subtle">
                <th className="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest w-40">Evento / Fecha</th>
                <th className="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Cliente</th>
                <th className="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Tipo</th>
                <th className="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Estado Contrato</th>
                <th className="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-right">Acciones</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {events.map((event) => (
                <tr key={event.id} className="hover:bg-[#f8fafc] transition-colors group">
                  <td className="px-6 py-4">
                    <p className="text-[0.75rem] font-bold text-slate-900 mb-0.5">{event.id}</p>
                    <div className="flex items-center gap-1.5 text-slate-400">
                      <CalendarIcon size={12} />
                      <span className="text-[0.7rem] font-medium">{event.date}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <p className="text-[0.875rem] font-bold text-slate-800 leading-tight">{event.clientName}</p>
                    <p className="text-[0.7rem] text-slate-400 mt-1">{event.guests} pax</p>
                  </td>
                  <td className="px-6 py-4 text-center">
                    <span className={`px-3 py-1 rounded-full text-[0.65rem] font-bold border ${getEventTypeColor(event.eventType)}`}>
                      {event.eventType}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-center">
                    {event.signedContractUrl ? (
                      <span className="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[0.65rem] font-bold border border-emerald-100">
                        <CheckCircle size={14} />
                        Firmado
                      </span>
                    ) : (
                      <span className="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 text-slate-400 rounded-full text-[0.65rem] font-bold border border-slate-200">
                        <AlertCircle size={14} />
                        Pendiente
                      </span>
                    )}
                  </td>
                  <td className="px-6 py-4">
                    <div className="flex items-center justify-end gap-2 text-slate-400">
                      <button 
                         onClick={() => setSelectedEventDetails(event)}
                         className="p-2 hover:bg-slate-100 hover:text-brand-accent rounded-lg transition-all"
                         title="Ver Detalles"
                      >
                        <Eye size={18} />
                      </button>
                      <button 
                        onClick={() => generatePDF(event)}
                        className="p-2 hover:bg-slate-100 hover:text-blue-600 rounded-lg transition-all"
                        title="Descargar Contrato"
                      >
                        <Download size={18} />
                      </button>
                      {!isCM && (
                        <>
                          <button 
                            onClick={() => setEventToUpload(event)}
                            className="p-2 hover:bg-slate-100 hover:text-emerald-600 rounded-lg transition-all"
                            title="Subir Contrato Firmado"
                          >
                            <FileUp size={18} />
                          </button>
                          {canDelete && (
                            <button 
                              onClick={() => onDeleteEvent?.(event.id)}
                              className="p-2 hover:bg-red-50 hover:text-red-500 rounded-lg transition-all"
                              title="Eliminar registro"
                            >
                              <Trash2 size={18} />
                            </button>
                          )}
                        </>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        ) : (
          renderCalendar()
        )}
      </div>

      <AnimatePresence>
        {selectedEventDetails && (
          <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <motion.div 
              initial={{ scale: 0.9, opacity: 0, y: 20 }}
              animate={{ scale: 1, opacity: 1, y: 0 }}
              exit={{ scale: 0.9, opacity: 0, y: 20 }}
              className="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100"
            >
              <div className="relative h-32 bg-slate-900 p-8 flex items-end">
                <div className="absolute top-4 right-4">
                  <button onClick={() => setSelectedEventDetails(null)} className="p-2 hover:bg-white/10 rounded-full text-white/60 transition-colors">
                    <X size={20} />
                  </button>
                </div>
                <div>
                   <span className={`px-3 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-widest border ${getEventTypeColor(selectedEventDetails.eventType)} border-white/20 bg-white/10`}>
                     {selectedEventDetails.eventType}
                   </span>
                   <h3 className="text-2xl font-black text-white tracking-tight mt-2">{selectedEventDetails.clientName}</h3>
                </div>
              </div>

              <div className="p-8 grid grid-cols-2 gap-8 bg-white">
                <div className="space-y-6">
                  <div>
                    <label className="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest block mb-1">Monto Total</label>
                    <p className="text-xl font-bold text-slate-900">${selectedEventDetails.totalAmount.toLocaleString()}</p>
                  </div>
                  <div>
                    <label className="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest block mb-1">Pagado Inicial</label>
                    <p className="text-xl font-bold text-emerald-600">${selectedEventDetails.advancePayment.toLocaleString()}</p>
                  </div>
                  <div>
                    <label className="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest block mb-1">Saldo Final</label>
                    <p className="text-2xl font-black text-red-600">${selectedEventDetails.balancePending.toLocaleString()}</p>
                  </div>
                </div>
                
                <div className="space-y-6">
                  <div>
                    <label className="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest block mb-1">Fecha del Evento</label>
                    <p className="text-sm font-bold text-slate-700">{selectedEventDetails.date}</p>
                  </div>
                  <div>
                    <label className="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest block mb-1">Limite Pago Final</label>
                    <p className="text-sm font-bold text-amber-600">{selectedEventDetails.paymentDueDate}</p>
                  </div>
                  <div>
                    <label className="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest block mb-1">Contrato</label>
                    <div className="flex items-center gap-2">
                       {selectedEventDetails.signedContractUrl ? (
                         <span className="text-[0.7rem] font-bold text-emerald-600 flex items-center gap-1">
                           <CheckCircle size={14} /> Firmado & Subido
                         </span>
                       ) : (
                         <span className="text-[0.7rem] font-bold text-slate-400 flex items-center gap-1">
                           <AlertCircle size={14} /> Pendiente de Firma
                         </span>
                       )}
                    </div>
                  </div>
                </div>
              </div>

              <div className="p-6 bg-slate-50 border-t border-slate-100 flex flex-wrap gap-3">
                 <button 
                   onClick={() => generatePDF(selectedEventDetails)}
                   className="flex-1 min-w-[200px] bg-white border border-slate-200 py-3 rounded-xl font-bold text-slate-700 hover:bg-slate-100 transition-all flex items-center justify-center gap-2"
                 >
                   <Download size={18} />
                   Descargar Contrato
                 </button>
                 {!isCM && (
                   <button 
                     onClick={() => {
                       setEventToUpload(selectedEventDetails);
                       setSelectedEventDetails(null);
                     }}
                     className="flex-1 min-w-[200px] bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-black transition-all flex items-center justify-center gap-2 shadow-lg shadow-slate-200"
                   >
                     <FileUp size={18} />
                     Subir Firmado
                   </button>
                 )}
                 {selectedEventDetails.balancePending > 0 && !isCM && (
                   <div className="w-full mt-2">
                     {!isConfirmingPay ? (
                       <button 
                         onClick={() => setIsConfirmingPay(true)}
                         className="w-full bg-emerald-600 text-white py-4 rounded-xl font-black uppercase text-xs tracking-[0.2em] hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100 flex items-center justify-center gap-2"
                       >
                         <DollarSign size={20} />
                         Saldar Deuda Total
                       </button>
                     ) : (
                       <div className="bg-emerald-50 border border-emerald-100 rounded-xl p-4 flex flex-col gap-3 animate-in fade-in slide-in-from-top-1">
                         <p className="text-[0.7rem] font-bold text-emerald-800 text-center uppercase tracking-widest leading-relaxed">
                           ¿Confirmar pago total de <span className="text-emerald-600">${selectedEventDetails.balancePending.toLocaleString()}</span>?
                         </p>
                         <div className="flex gap-2">
                           <button 
                             onClick={() => setIsConfirmingPay(false)}
                             className="flex-1 bg-white border border-emerald-200 py-2 rounded-lg text-[0.65rem] font-bold text-slate-500 hover:bg-emerald-100 transition-colors"
                           >
                             Cancelar
                           </button>
                           <button 
                             onClick={() => handlePayBalance(selectedEventDetails.id)}
                             className="flex-1 bg-emerald-600 py-2 rounded-lg text-[0.65rem] font-bold text-white hover:bg-emerald-700 shadow-sm transition-colors"
                           >
                             Sí, Confirmar
                           </button>
                         </div>
                       </div>
                     )}
                   </div>
                 )}
              </div>
            </motion.div>
          </div>
        )}

        {eventToUpload && (
          <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <motion.div 
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden"
            >
              <div className="p-8 text-center space-y-6">
                <div className="w-16 h-16 bg-blue-50 text-brand-accent rounded-2xl flex items-center justify-center mx-auto shadow-sm">
                  <FileUp size={32} />
                </div>
                <div className="space-y-2">
                  <h3 className="text-xl font-black text-slate-900 tracking-tight">Subir Contrato Firmado</h3>
                  <p className="text-sm text-slate-500">Seleccione el archivo PDF o imagen del contrato firmado para {eventToUpload.clientName}.</p>
                </div>
                
                <div className="p-8 border-2 border-dashed border-slate-100 rounded-2xl bg-slate-50/50 cursor-pointer hover:border-brand-accent transition-all group">
                   <Plus size={24} className="mx-auto text-slate-300 group-hover:text-brand-accent transition-colors" />
                   <p className="text-[0.65rem] font-bold text-slate-400 mt-2 uppercase tracking-widest">Haz click para seleccionar</p>
                </div>

                <div className="flex gap-3">
                  <button onClick={() => setEventToUpload(null)} className="flex-1 py-3 font-bold text-slate-500 hover:bg-slate-50 rounded-xl transition-all">Cancelar</button>
                  <button 
                    onClick={() => handleUploadContract(eventToUpload.id)}
                    className="flex-1 bg-brand-accent text-white py-3 font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-600 transition-all"
                  >
                    Confirmar Subida
                  </button>
                </div>
              </div>
            </motion.div>
          </div>
        )}

        {showModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div 
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-border-subtle"
            >
              <div className="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
                <h3 className="font-bold text-text-main">Registrar Nuevo Evento</h3>
                <button onClick={() => setShowModal(false)} className="text-text-muted hover:text-text-main transition-colors">
                  <X size={20} />
                </button>
              </div>
              <form onSubmit={handleCreateEvent} className="p-6 flex flex-col gap-4">
                <div className="flex flex-col gap-1.5">
                  <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Nombre del Cliente</label>
                  <input 
                    required 
                    type="text" 
                    className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm focus:ring-1 focus:ring-brand-accent/40 outline-none"
                    value={newEvent.clientName}
                    onChange={(e) => setNewEvent({...newEvent, clientName: e.target.value})}
                  />
                </div>
                <div className="flex flex-col gap-1.5">
                  <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">CI / Documento de Identidad</label>
                  <input 
                    required 
                    type="text" 
                    className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm focus:ring-1 focus:ring-brand-accent/40 outline-none"
                    value={newEvent.clientId}
                    onChange={(e) => setNewEvent({...newEvent, clientId: e.target.value})}
                  />
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Fecha</label>
                    <input 
                      required 
                      type="date" 
                      className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none"
                      value={newEvent.date}
                      onChange={(e) => setNewEvent({...newEvent, date: e.target.value})}
                    />
                  </div>
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Tipo</label>
                    <select 
                      className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none"
                      value={newEvent.eventType}
                      onChange={(e) => setNewEvent({...newEvent, eventType: e.target.value})}
                    >
                      {eventTypes.map(type => (
                        <option key={type} value={type}>{type}</option>
                      ))}
                    </select>
                  </div>
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Invitados</label>
                    <input 
                      required 
                      type="number" 
                      className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none"
                      value={isNaN(newEvent.guests) ? '' : newEvent.guests}
                      onChange={(e) => setNewEvent({...newEvent, guests: e.target.value === '' ? 0 : parseInt(e.target.value)})}
                    />
                  </div>
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Monto Total ($)</label>
                    <input 
                      required 
                      type="number" 
                      className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none"
                      value={isNaN(newEvent.totalAmount) ? '' : newEvent.totalAmount}
                      onChange={(e) => setNewEvent({...newEvent, totalAmount: e.target.value === '' ? 0 : parseInt(e.target.value)})}
                    />
                  </div>
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Adelanto ($)</label>
                    <input 
                      required 
                      type="number" 
                      className="px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-[6px] text-sm outline-none text-emerald-800"
                      value={isNaN(newEvent.advancePayment) ? '' : newEvent.advancePayment}
                      onChange={(e) => setNewEvent({...newEvent, advancePayment: e.target.value === '' ? 0 : parseInt(e.target.value)})}
                    />
                  </div>
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Fecha Límite Pago</label>
                    <input 
                      required 
                      type="date" 
                      className="px-4 py-2 bg-amber-50 border border-amber-100 rounded-[6px] text-sm outline-none text-amber-800"
                      value={newEvent.paymentDueDate}
                      onChange={(e) => setNewEvent({...newEvent, paymentDueDate: e.target.value})}
                    />
                  </div>
                </div>
                <div className="bg-slate-50 p-3 rounded-lg border border-border-subtle">
                  <div className="flex justify-between items-center text-[0.7rem] font-bold">
                    <span className="text-text-muted uppercase tracking-widest">Saldo Pendiente:</span>
                    <span className="text-red-600 text-sm">
                      ${((newEvent.totalAmount || 0) - (newEvent.advancePayment || 0)).toLocaleString()}
                    </span>
                  </div>
                </div>
                <button type="submit" className="mt-4 bg-brand-accent text-white py-2.5 rounded-[6px] font-bold hover:bg-blue-600 transition-all shadow-md">
                  Confirmar Reserva
                </button>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      <AnimatePresence>
        {showTypesModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div 
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden border border-border-subtle"
            >
              <div className="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
                <h3 className="font-bold text-text-main">Gestionar Tipos de Eventos</h3>
                <button onClick={() => setShowTypesModal(false)} className="text-text-muted hover:text-text-main transition-colors">
                  <X size={20} />
                </button>
              </div>
              <div className="p-6 flex flex-col gap-4">
                <form onSubmit={handleAddEventType} className="flex gap-2">
                  <input 
                    type="text" 
                    placeholder="Nuevo tipo (ej. Bautizo)"
                    className="flex-1 px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none"
                    value={newType}
                    onChange={(e) => setNewType(e.target.value)}
                  />
                  <button type="submit" className="bg-slate-800 text-white p-2 rounded-lg hover:bg-slate-700">
                    <Plus size={20} />
                  </button>
                </form>

                <div className="flex flex-col gap-2 max-h-[300px] overflow-y-auto">
                  {eventTypes.map(type => (
                    <div key={type} className="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-100 group">
                      <span className="text-sm font-medium text-slate-700">{type}</span>
                      <button 
                        onClick={() => handleDeleteEventType(type)}
                        className="text-slate-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100"
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>
                  ))}
                  {eventTypes.length === 0 && <p className="text-center text-xs text-slate-400 py-4 italic">No hay tipos registrados.</p>}
                </div>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </motion.div>
  );
};
