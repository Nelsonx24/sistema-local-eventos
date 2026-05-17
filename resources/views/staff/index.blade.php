@extends('layout.main')

@section('title', 'Personal - Gran Cañaveral')
@section('header-title', 'Personal')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center px-6 py-4 bg-white rounded-lg border border-border-subtle shadow-sm">
        <h3 class="font-semibold text-text-main">Personal y Trabajadores</h3>
        <button onclick="openModal('staff-modal')" class="bg-brand-primary text-white px-4 py-2 rounded-[6px] text-[0.8rem] font-bold hover:bg-slate-800 transition-all flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Registrar Trabajador
        </button>
    </div>

    <div class="bg-white rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-[#f8fafc] border-b border-border-subtle">
                    <th class="px-6 py-3 text-[0.75rem] font-bold text-text-muted uppercase tracking-widest">Trabajador</th>
                    <th class="px-6 py-3 text-[0.75rem] font-bold text-text-muted uppercase tracking-widest">Cargo / Función</th>
                    <th class="px-6 py-3 text-[0.75rem] font-bold text-text-muted uppercase tracking-widest text-center">Estado</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1f5f9]">
                @forelse($staff as $member)
                <tr class="hover:bg-[#f8fafc] transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->avatar }}" alt="{{ $member->name }}" class="w-10 h-10 rounded-full border border-border-subtle shadow-sm">
                            <div>
                                <p class="font-bold text-[0.875rem] text-text-main">{{ $member->name }}</p>
                                <p class="text-[0.75rem] text-text-muted">{{ $member->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 text-[0.875rem] text-text-main">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            {{ $member->role }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 rounded-full text-[0.7rem] font-semibold {{ $member->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $member->status === 'Active' ? 'bg-emerald-500' : 'bg-slate-400' }} inline-block mr-1"></span>
                            {{ $member->status === 'Active' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form method="POST" action="{{ route('staff.destroy', $member->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-text-muted hover:text-red-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                        No hay personal registrado en el sistema.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Staff Modal -->
<div id="staff-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main">Registrar Trabajador</h3>
            <button onclick="closeModal('staff-modal')" class="text-text-muted hover:text-text-main"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('staff.store') }}" class="p-6 flex flex-col gap-4 text-left">
            @csrf
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase">Nombre Completo</label>
                <input required type="text" name="name" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase">Correo</label>
                <input required type="email" name="email" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Cargo</label>
                    <select name="role" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                        <option value="Administrador">Administrador</option>
                        <option value="Vendedor">Vendedor</option>
                        <option value="CM">CM</option>
                        <option value="Mesero">Mesero</option>
                        <option value="Seguridad">Seguridad</option>
                        <option value="Limpieza">Limpieza</option>
                        <option value="Catering">Catering</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Estado</label>
                    <select name="status" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                        <option value="Active">Activo</option>
                        <option value="On Leave">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Usuario</label>
                    <input type="text" name="username" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Password</label>
                    <input type="text" name="password" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-brand-primary text-white py-2.5 rounded-lg font-bold">Registrar Trabajador</button>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>
@endsection