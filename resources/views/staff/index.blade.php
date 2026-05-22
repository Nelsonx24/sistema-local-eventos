@extends('layout.main')

@section('title', 'Personal - Gran Cañaveral')
@section('header-title', 'Personal')

@section('content')
<div class="flex flex-col gap-6">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="flex justify-between items-center px-6 py-4 bg-white rounded-lg border border-border-subtle shadow-sm">
        <h3 class="font-semibold text-text-main">Personal y Trabajadores</h3>
        <button onclick="openStaffModal()" class="bg-brand-primary text-white px-4 py-2 rounded-[6px] text-[0.8rem] font-bold hover:bg-slate-800 transition-all flex items-center gap-2">
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
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="viewStaff('{{ $member->id }}')" class="p-2 text-text-muted hover:text-blue-500 transition-colors" title="Ver">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button onclick="editStaff('{{ $member->id }}')" class="p-2 text-text-muted hover:text-amber-500 transition-colors" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                            </button>
                            <button onclick="changePassword('{{ $member->id }}', '{{ $member->name }}')" class="p-2 text-text-muted hover:text-purple-500 transition-colors" title="Cambiar Contraseña">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z"/><circle cx="16.5" cy="7.5" r=".5"/></svg>
                            </button>
                            <form method="POST" action="{{ route('staff.destroy', $member->id) }}" onsubmit="return confirm('¿Eliminar este trabajador?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-text-muted hover:text-red-500 transition-colors" title="Eliminar">
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

<!-- Staff Form Modal (Create / Edit) -->
<div id="staff-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 id="staff-modal-title" class="font-bold text-text-main">Registrar Trabajador</h3>
            <button onclick="closeModal('staff-modal')" class="text-text-muted hover:text-text-main"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <form id="staff-form" method="POST" action="{{ route('staff.store') }}" class="p-6 flex flex-col gap-4 text-left">
            @csrf
            <input type="hidden" name="_method" id="staff-method" value="">
            <input type="hidden" name="staff_id" id="staff-id" value="">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Nombre</label>
                    <input required id="staff-first-name" type="text" name="first_name" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Apellido</label>
                    <input required id="staff-last-name" type="text" name="last_name" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase">Correo</label>
                <input required id="staff-email" type="email" name="email" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Cargo</label>
                    <select id="staff-role" name="role" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
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
                    <select id="staff-status" name="status" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                        <option value="Active">Activo</option>
                        <option value="On Leave">Inactivo</option>
                    </select>
                </div>
            </div>
            <div id="staff-credentials-fields" class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Usuario</label>
                    <input required id="staff-username" type="text" name="username" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Password</label>
                    <input required id="staff-password" type="password" name="password" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" minlength="8">
                </div>
            </div>
            <button id="staff-submit-btn" type="submit" class="mt-4 bg-brand-primary text-white py-2.5 rounded-lg font-bold">Registrar Trabajador</button>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="password-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main">Cambiar Contraseña</h3>
            <button onclick="closeModal('password-modal')" class="text-text-muted hover:text-text-main"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <form id="password-form" method="POST" action="" class="p-6 flex flex-col gap-4 text-left">
            @csrf
            <p class="text-sm text-text-muted" id="password-staff-name"></p>
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase">Nueva Contraseña</label>
                <input required id="new-password" type="text" name="password" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
            </div>
            <button type="submit" class="mt-2 bg-purple-600 text-white py-2.5 rounded-lg font-bold hover:bg-purple-700 transition-colors">Guardar Contraseña</button>
        </form>
    </div>
</div>

<!-- View Staff Modal -->
<div id="view-staff-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main">Detalles del Trabajador</h3>
            <button onclick="closeModal('view-staff-modal')" class="text-text-muted hover:text-text-main"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <div class="p-6 flex flex-col gap-4 text-left">
            <div class="flex items-center gap-4 mb-2">
                <img id="view-avatar" src="" alt="" class="w-16 h-16 rounded-full border border-border-subtle shadow-sm">
                <div>
                    <p id="view-name" class="font-bold text-lg text-text-main"></p>
                    <p id="view-email" class="text-sm text-text-muted"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 rounded-lg">
                <div>
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Cargo / Función</label>
                    <p id="view-role" class="text-text-main flex items-center gap-2 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span></span>
                    </p>
                </div>
                <div>
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Estado</label>
                    <p id="view-status" class="text-text-main mt-1"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Usuario</label>
                    <p id="view-username" class="text-text-main mt-1">—</p>
                </div>
                <div>
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Registrado</label>
                    <p id="view-created" class="text-text-main mt-1"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

var hasErrors = {!! json_encode($errors->any()) !!};
if (hasErrors) {
    openStaffModal();
}

function openStaffModal() {
    document.getElementById('staff-form').action = '{{ route("staff.store") }}';
    document.getElementById('staff-method').value = '';
    document.getElementById('staff-id').value = '';
    document.getElementById('staff-modal-title').textContent = 'Registrar Trabajador';
    document.getElementById('staff-submit-btn').textContent = 'Registrar Trabajador';
    document.getElementById('staff-first-name').value = '';
    document.getElementById('staff-last-name').value = '';
    document.getElementById('staff-email').value = '';
    document.getElementById('staff-role').value = 'Administrador';
    document.getElementById('staff-status').value = 'Active';
    document.getElementById('staff-username').value = '';
    document.getElementById('staff-password').value = '';
    document.getElementById('staff-credentials-fields').classList.remove('hidden');
    openModal('staff-modal');
}

function editStaff(id) {
    fetch('/staff/' + id + '/edit')
        .then(r => r.json())
        .then(data => {
            document.getElementById('staff-form').action = '/staff/' + id;
            document.getElementById('staff-method').value = 'PUT';
            document.getElementById('staff-id').value = data.id;
            document.getElementById('staff-modal-title').textContent = 'Editar Trabajador';
            document.getElementById('staff-submit-btn').textContent = 'Actualizar Trabajador';
            document.getElementById('staff-first-name').value = data.first_name || data.name || '';
            document.getElementById('staff-last-name').value = data.last_name || '';
            document.getElementById('staff-email').value = data.email;
            document.getElementById('staff-role').value = data.role;
            document.getElementById('staff-status').value = data.status;
            document.getElementById('staff-username').value = data.username || '';
            document.getElementById('staff-password').value = '';
            document.getElementById('staff-credentials-fields').classList.add('hidden');
            openModal('staff-modal');
        });
}

function changePassword(id, name) {
    document.getElementById('password-form').action = '/staff/' + id + '/password';
    document.getElementById('password-staff-name').textContent = 'Cambiando contraseña de: ' + name;
    document.getElementById('new-password').value = '';
    openModal('password-modal');
}

function viewStaff(id) {
    fetch('/staff/' + id)
        .then(r => r.json())
        .then(data => {
            document.getElementById('view-avatar').src = data.avatar;
            document.getElementById('view-name').textContent = (data.first_name || '') + ' ' + (data.last_name || '');
            document.getElementById('view-email').textContent = data.email;
            document.getElementById('view-role').querySelector('span').textContent = data.role;
            const statusEl = document.getElementById('view-status');
            if (data.status === 'Active') {
                statusEl.innerHTML = '<span class="px-2 py-1 rounded-full text-[0.7rem] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>Activo</span>';
            } else {
                statusEl.innerHTML = '<span class="px-2 py-1 rounded-full text-[0.7rem] font-semibold bg-slate-50 text-slate-500 border border-slate-200"><span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block mr-1"></span>Inactivo</span>';
            }
            document.getElementById('view-username').textContent = data.username || '—';
            document.getElementById('view-created').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : '—';
            openModal('view-staff-modal');
        });
}
</script>
@endsection
