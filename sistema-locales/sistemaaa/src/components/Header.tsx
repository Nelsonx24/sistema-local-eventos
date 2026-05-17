import { Search, Bell, Settings, User } from 'lucide-react';

export const Header = ({ title }: { title: string }) => {
  return (
    <header id="main-header" className="h-16 border-b border-border-subtle bg-white sticky top-0 z-10 px-6 flex items-center justify-between">
      <div className="flex items-center gap-3">
        <span className="bg-slate-100 text-slate-600 text-[10px] uppercase font-bold px-2 py-1 rounded border border-slate-200">Uso Interno Administrativo</span>
        <h2 className="text-sm font-semibold text-text-muted uppercase tracking-widest border-l border-slate-200 pl-3">{title}</h2>
      </div>

      <div className="flex items-center gap-6">
        <div className="relative group hidden md:block">
          <input 
            type="text" 
            placeholder="Buscar registros, facturas o clientes..." 
            className="pl-4 pr-4 py-[7px] bg-[#f1f5f9] border border-border-subtle rounded-[6px] text-xs w-[300px] focus:outline-none focus:ring-1 focus:ring-brand-accent/40 transition-all text-[#64748b]"
          />
        </div>

        <div className="flex items-center gap-4">
          <button className="text-text-muted hover:text-text-main transition-colors relative">
            <Bell size={18} />
            <span className="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full border-2 border-white" />
          </button>
          
          <div className="w-[1px] h-6 bg-border-subtle" />

          <div className="flex items-center gap-3">
            <div className="text-right hidden sm:block">
              <p className="text-[0.85rem] font-semibold text-text-main leading-none">Alejandro Moreno</p>
              <p className="text-[0.7rem] text-text-muted mt-1">Administrador</p>
            </div>
            <div className="w-8 h-8 rounded-full bg-brand-accent flex items-center justify-center text-white font-bold text-xs overflow-hidden shadow-sm">
              AM
            </div>
          </div>
        </div>
      </div>
    </header>
  );
};
