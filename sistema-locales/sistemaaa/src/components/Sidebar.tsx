import { LucideIcon, UserCircle, LogOut } from 'lucide-react';
import { motion } from 'motion/react';
import { StaffRole } from '../types';

interface SidebarItemProps {
  key?: string;
  icon: LucideIcon;
  label: string;
  active: boolean;
  onClick: () => void;
}

const SidebarItem = ({ icon: Icon, label, active, onClick }: SidebarItemProps) => (
  <button
    id={`sidebar-item-${label.toLowerCase()}`}
    onClick={onClick}
    className={`w-full flex items-center gap-3 px-6 py-3 transition-all duration-200 text-sm ${
      active 
        ? 'bg-[#1e293b] text-white border-l-4 border-brand-accent' 
        : 'text-[#f8fafc]/70 hover:bg-[#1e293b] hover:text-[#f8fafc]'
    }`}
  >
    <Icon size={18} />
    <span className="font-medium">{label}</span>
  </button>
);

interface SidebarProps {
  currentView: string;
  onViewChange: (view: string) => void;
  items: { id: string; label: string; icon: LucideIcon }[];
  userRole: StaffRole;
  onRoleChange: (role: StaffRole) => void;
  onLogout: () => void;
}

export const Sidebar = ({ currentView, onViewChange, items, userRole, onLogout }: SidebarProps) => {
  return (
    <aside id="main-sidebar" className="w-[240px] h-screen bg-[#0f172a] flex flex-col fixed left-0 top-0 border-right border-[#1e293b]">
      <div className="p-6 h-[64px] flex items-center gap-3 border-b border-[#1e293b]">
        <div className="w-6 h-6 bg-brand-accent rounded" />
        <h1 className="text-lg font-bold tracking-tight text-[#f8fafc]">Gran Cañaveral</h1>
      </div>

      <nav className="flex-1 py-4">
        {items.map((item) => (
          <SidebarItem
            key={item.id}
            icon={item.icon}
            label={item.label}
            active={currentView === item.id}
            onClick={() => onViewChange(item.id)}
          />
        ))}
      </nav>

      {/* User Info & Logout */}
      <div className="p-4 mx-4 mb-4 bg-slate-800/50 rounded-2xl border border-slate-700/30">
        <div className="flex items-center gap-3 mb-4">
          <div className="w-8 h-8 bg-brand-accent text-white rounded-lg flex items-center justify-center font-bold text-xs shadow-lg shadow-brand-accent/20">
            {userRole.charAt(0)}
          </div>
          <div className="overflow-hidden">
            <p className="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-none mb-1">Usuario</p>
            <p className="text-white text-xs font-bold truncate">{userRole}</p>
          </div>
        </div>
        
        <button 
          onClick={onLogout}
          className="w-full flex items-center justify-center gap-2 py-2.5 bg-red-500/10 text-red-500 rounded-xl text-[11px] font-bold hover:bg-red-500 hover:text-white transition-all group"
        >
          <LogOut size={14} className="group-hover:-translate-x-1 transition-transform" />
          Cerrar Sesión
        </button>
      </div>

      <div className="p-6 text-[0.75rem] text-[#f8fafc]/30 border-t border-[#1e293b]">
        <p className="font-medium">Gran Cañaveral &copy; 2024</p>
      </div>
    </aside>
  );
};
