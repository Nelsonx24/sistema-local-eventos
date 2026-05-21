@extends('layout.main')

@section('title', 'Configuración - Gran Cañaveral')
@section('header-title', 'Configuración')

@section('content')
<div class="bg-white rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)] p-8">
    <div class="flex flex-col md:flex-row gap-6 py-8 border-b border-border-subtle">
        <div class="md:w-64 flex-shrink-0">
            <h4 class="font-bold text-gray-900">Profile Information</h4>
            <p class="text-sm text-gray-500 mt-1">Update your personal details and how others see you.</p>
        </div>
        <div class="flex-1 max-w-xl flex flex-col gap-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">First Name</label>
                    <input type="text" value="{{ Auth::guard('staff')->user()->name }}" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-accent/10 focus:border-brand-accent outline-none text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Last Name</label>
                    <input type="text" value="" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-accent/10 focus:border-brand-accent outline-none text-sm">
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Email Address</label>
                <input type="email" value="{{ Auth::guard('staff')->user()->email }}" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-accent/10 focus:border-brand-accent outline-none text-sm">
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-6 py-8 border-b border-border-subtle">
        <div class="md:w-64 flex-shrink-0">
            <h4 class="font-bold text-gray-900">System Preferences</h4>
            <p class="text-sm text-gray-500 mt-1">Configure how the system behaves and looks.</p>
        </div>
        <div class="flex-1 max-w-xl flex flex-col gap-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    <div>
                        <p class="text-sm font-bold">Dark Mode</p>
                        <p class="text-xs text-gray-500">Enable a darker theme for reduced eye strain.</p>
                    </div>
                </div>
                <div class="w-10 h-6 bg-gray-200 rounded-full relative cursor-not-allowed">
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all"></div>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <div>
                        <p class="text-sm font-bold">System Language</p>
                        <p class="text-xs text-gray-500">Set the default language for the interface.</p>
                    </div>
                </div>
                <select class="bg-transparent text-sm font-bold text-brand-accent outline-none">
                    <option>Spanish (ES)</option>
                    <option>English (US)</option>
                </select>
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-8">
        <button class="bg-brand-primary text-white px-8 py-3 rounded-xl font-bold flex items-center gap-2 hover:bg-gray-800 transition-all active:scale-95 shadow-lg shadow-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Changes
        </button>
    </div>
</div>
@endsection
