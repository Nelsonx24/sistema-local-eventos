<div class="flex flex-col gap-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Cliente</p>
            <p class="font-bold text-slate-800">{{ $event->client_name }}</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">CI</p>
            <p class="font-medium text-slate-700">{{ $event->client_id }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Fecha</p>
            <p class="font-medium text-slate-700">{{ $event->date }}</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tipo</p>
            <span class="px-2 py-1 bg-slate-100 rounded text-xs font-bold">{{ $event->event_type }}</span>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Invitados</p>
            <p class="font-medium text-slate-700">{{ $event->guests }} personas</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Monto Total</p>
            <p class="font-bold text-emerald-600">{{ number_format($event->total_amount) }} Bs</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Adelanto</p>
            <p class="font-bold text-blue-600">{{ number_format($event->advance_payment) }} Bs</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Saldo Pendiente</p>
            <p class="font-bold text-amber-600">{{ number_format($event->total_amount - $event->advance_payment) }} Bs</p>
        </div>
    </div>
    <div class="pt-4 border-t border-slate-100 flex gap-2">
        <a href="{{ route('events.download-contract', $event->id) }}" target="_blank" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold text-center hover:bg-blue-700 transition-all">
            Descargar Contrato
        </a>
        <a href="{{ route('reports.show', $event->id) }}" class="flex-1 bg-slate-800 text-white py-2 rounded-lg font-bold text-center hover:bg-slate-900 transition-all">
            Ver Reporte
        </a>
    </div>
</div>