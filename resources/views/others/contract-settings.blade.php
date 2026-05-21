@extends('layout.main')

@section('title', 'Configuración de Contratos - Gran Cañaveral')
@section('header-title', 'Otros')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('others.index') }}" class="text-slate-500 hover:text-slate-900 font-bold text-sm flex items-center gap-2 transition-colors mb-4">
        ← Volver al menú
    </a>
    
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-brand-gold/10 text-brand-gold rounded-2xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Datos Generales del Contrato</h2>
                <p class="text-sm text-slate-500">Configura la información legal que aparecerá en los contratos</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-6">
        <form method="POST" action="{{ route('others.contract-settings.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nombre del Salón</label>
                    <input type="text" name="salon_name" value="{{ $settings['salon_name'] }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Representante Legal</label>
                    <input type="text" name="representative" value="{{ $settings['representative'] }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">CI del Representante</label>
                    <input type="text" name="representative_ci" value="{{ $settings['representative_ci'] }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ciudad de Firma</label>
                    <input type="text" name="city" value="{{ $settings['city'] }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                </div>
            </div>

            <hr class="my-6 border-slate-200">

            <div class="flex flex-col gap-4">
                <h4 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Marca de Agua</h4>
                <div class="flex items-center gap-6">
                    <div class="flex flex-col gap-2 flex-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Imagen para marca de agua</label>
                        <input type="file" name="watermark" accept="image/*" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-brand-gold/10 file:text-brand-gold-dark file:font-bold file:text-xs hover:file:bg-brand-gold/20">
                        <p class="text-[10px] text-slate-400">Recomendado: PNG con transparencia, máximo 2MB. Aparecerá al centro del contrato.</p>
                    </div>
                    @php
                        $watermarkPath = \App\Models\Config::getWatermark();
                        $watermarkExists = $watermarkPath && file_exists(storage_path('app/public/'.$watermarkPath));
                    @endphp
                    @if($watermarkExists)
                    <div class="w-20 h-20 rounded-xl border border-slate-200 overflow-hidden bg-slate-50 shrink-0">
                        <img src="{{ asset('storage/'.$watermarkPath) }}" class="w-full h-full object-contain">
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex gap-3 items-start mt-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-gold shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Estos datos se utilizarán automáticamente para rellenar los espacios correspondientes en el contrato del evento al generar el PDF.
                </p>
            </div>
            
            <button type="submit" class="mt-6 px-8 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all shadow-xl bg-black text-white shadow-black/20 hover:bg-slate-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar Cambios
            </button>
        </form>
    </div>
</div>
@endsection
