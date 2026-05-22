@extends('layout.main')

@section('title', 'Activos - Gran Cañaveral')
@section('header-title', 'Otros')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <a href="{{ route('others.index') }}" class="text-slate-500 hover:text-slate-900 font-bold text-sm flex items-center gap-2 transition-colors mb-4">
        ← Volver al menú
    </a>

    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Inventario de Activos</h2>
                <p class="text-sm text-slate-500">Gestión de equipamiento de Cocina y Cantina</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('others.assets.pdf') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-2xl font-bold flex items-center gap-2 hover:bg-slate-50 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Descargar PDF
            </a>
            <button onclick="openModal('asset-modal')" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 hover:bg-black transition-all shadow-xl shadow-slate-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Registrar Activo
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Activo</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Categoría</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Cantidad</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($assets as $asset)
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $asset->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $asset->category === 'Cocina' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'bg-brand-gold/10 text-brand-gold border border-brand-gold/20' }}">
                            {{ $asset->category }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-mono font-bold text-slate-900">{{ $asset->quantity }}</td>
                    <td class="px-6 py-4">
                        <span class="flex items-center gap-1.5 text-xs font-bold {{ $asset->condition === 'Bueno' ? 'text-emerald-600' : ($asset->condition === 'Regular' ? 'text-amber-600' : 'text-red-600') }}">
                            <div class="w-1.5 h-1.5 rounded-full {{ $asset->condition === 'Bueno' ? 'bg-emerald-500' : ($asset->condition === 'Regular' ? 'bg-amber-500' : 'bg-red-500') }}"></div>
                            {{ $asset->condition }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form method="POST" action="{{ route('others.assets.destroy', $asset->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                        No hay activos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($assets->hasPages())
    <div class="mt-4">
        {{ $assets->links() }}
    </div>
    @endif
</div>

<!-- Asset Modal -->
<div id="asset-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main">Registrar Activo</h3>
            <button onclick="closeModal('asset-modal')"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('others.assets.store') }}" class="p-6 flex flex-col gap-4">
            @csrf
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase">Nombre del Activo</label>
                <input required type="text" name="name" placeholder="Ej: Licuadora Industrial" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase">Categoría</label>
                    <select name="category" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                        <option value="Cocina">Cocina</option>
                        <option value="Cantina">Cantina</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase">Cantidad</label>
                    <input required type="number" name="quantity" value="1" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase">Estado</label>
                <select name="condition" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                    <option value="Bueno">Bueno</option>
                    <option value="Regular">Regular</option>
                    <option value="Malo">Malo</option>
                </select>
            </div>
            <button type="submit" class="mt-4 bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg shadow-emerald-100 hover:bg-emerald-700">Guardar Activo</button>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>
@endsection
