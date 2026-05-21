@extends('layout.main')

@section('title', 'Tienda - Gran Cañaveral')
@section('header-title', 'Tienda')

@section('content')
<div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <a href="{{ route('store.products') }}" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-brand-accent transition-all text-left flex flex-col gap-4 group">
        <div class="w-14 h-14 bg-brand-gold/10 text-brand-gold rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900 tracking-tight">Tienda</h3>
            <p class="text-sm text-slate-500 mt-1">Administra productos como chocolates, bebidas y más.</p>
        </div>
    </a>

    <a href="{{ route('store.gifts') }}" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-brand-accent transition-all text-left flex flex-col gap-4 group">
        <div class="w-14 h-14 bg-brand-gold/10 text-brand-gold rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5 4.8 4.8 0 0 1 4.5 5 4.8 4.8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/></svg>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900 tracking-tight">Regalos</h3>
            <p class="text-sm text-slate-500 mt-1">Administra artículos de regalo como relojes, joyería y más.</p>
        </div>
    </a>
</div>
@endsection
