@extends('layout.main')

@section('title', 'Gestión QR - Gran Cañaveral')
@section('header-title', 'Otros')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('others.index') }}" class="text-slate-500 hover:text-slate-900 font-bold text-sm flex items-center gap-2 transition-colors mb-4">
        ← Volver al menú
    </a>

    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">Gestión de Cobro QR</h2>
                <p class="text-sm text-slate-500">Actualice la imagen del código QR que se muestra a los clientes al pagar.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('others.qr.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            @csrf
            <div class="space-y-6">
                <div class="p-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center gap-4 group hover:border-indigo-400 transition-all">
                    <div class="w-full aspect-square bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden relative">
                        <img src="{{ $qrImage }}" alt="QR Preview" class="w-full h-full object-contain p-2">
                    </div>
                    <input type="file" name="qr_image" accept="image/*" class="hidden" id="qr-input" onchange="previewQR(this)">
                    <label for="qr-input" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest cursor-pointer hover:text-indigo-500">Cambiar Imagen</label>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl flex gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-indigo-500 shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    <p class="text-xs text-indigo-700 leading-relaxed font-medium">
                        Asegúrese de que el código QR sea legible y contenga los datos correctos de su cuenta bancaria para evitar contratiempos en los cobros.
                    </p>
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg bg-indigo-600 text-white shadow-indigo-200 hover:bg-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewQR(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            input.previousElementSibling.querySelector('img').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection