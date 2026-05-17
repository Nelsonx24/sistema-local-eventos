@extends('layout.main')

@section('title', 'Notificaciones - Gran Cañaveral')
@section('header-title', 'Otros')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('others.index') }}" class="text-slate-500 hover:text-slate-900 font-bold text-sm flex items-center gap-2 transition-colors mb-4">
        ← Volver al menú
    </a>

    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Notificaciones de Eventos</h2>
            <p class="text-sm text-slate-500">Recibe avisos al instante sobre nuevos eventos y pagos</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-6 text-left">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="15" height="20" x="4.5" y="2" rx="2" ry="2"/></svg>
                </div>
                <h3 class="font-bold text-slate-900">Activar en Celular</h3>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">
                Para recibir notificaciones en su celular Android, abra este sistema en Chrome, vaya a opciones (⋮) y seleccione <b>"Instalar Aplicación"</b> o <b>"Añadir a pantalla de inicio"</b>.
            </p>
            <button onclick="copyLink()" class="w-full bg-slate-100 text-slate-700 py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 hover:bg-slate-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                Copiar Link para Celular
            </button>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-6 text-left">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                </div>
                <h3 class="font-bold text-slate-900">Permisos del Navegador</h3>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">
                Habilite las notificaciones en este dispositivo para recibir alertas incluso con la pestaña cerrada.
            </p>
            <button onclick="requestNotificationPermission()" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-blue-100 group">
                Habilitar Notificaciones
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="group-hover:translate-x-1 transition-transform"><line x1="5" x2="12" y1="12" y2="17"/><polyline points="17 12 12 17 7 12"/></svg>
            </button>
        </div>
    </div>

    <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white relative overflow-hidden text-left">
       <div class="relative z-10 space-y-4">
          <div class="flex items-center gap-2 text-indigo-400">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
              <h4 class="text-[10px] font-bold uppercase tracking-widest">Información para el CM</h4>
          </div>
          <p class="text-sm opacity-70 max-w-lg">
            El rol de <b>CM (Community Manager)</b> tiene acceso total de visualización al módulo de eventos para coordinar agendas y publicidad. Por seguridad, no puede modificar datos sensibles para evitar errores accidentales en la agenda oficial.
          </p>
          <div class="pt-4 flex gap-4">
              <div class="flex flex-col gap-1">
                  <span class="text-[10px] font-bold text-emerald-400">DISPONIBLE</span>
                  <span class="text-xs font-medium">Ver Agenda</span>
              </div>
              <div class="flex flex-col gap-1">
                  <span class="text-[10px] font-bold text-emerald-400">DISPONIBLE</span>
                  <span class="text-xs font-medium">Registrar Prospectos</span>
              </div>
              <div class="flex flex-col gap-1">
                  <span class="text-[10px] font-bold text-red-400">BLOQUEADO</span>
                  <span class="text-xs font-medium">Eliminar / Editar</span>
              </div>
          </div>
       </div>
       <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="absolute -right-8 -bottom-8 opacity-5 -rotate-12 text-white"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
    </div>
</div>

<script>
function copyLink() {
    navigator.clipboard.writeText(window.location.href);
    alert('¡Link copiado! Envíelo a su celular para instalar la PWA.');
}
function requestNotificationPermission() {
    if ('Notification' in window) {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                new Notification('¡Configurado!', { body: 'Las notificaciones del Salón Gran Cañaveral están activas.' });
            }
        });
    }
}
</script>
@endsection