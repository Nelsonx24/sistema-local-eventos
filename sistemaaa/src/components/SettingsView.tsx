import { motion } from 'motion/react';
import { Save, Globe, Moon } from 'lucide-react';
import type { ReactNode } from 'react';

const SettingSection = ({ title, description, children }: { title: string, description: string, children: ReactNode }) => (
  <div className="flex flex-col md:flex-row gap-6 py-8 border-b border-border-subtle last:border-0">
    <div className="md:w-64 flex-shrink-0">
      <h4 className="font-bold text-gray-900">{title}</h4>
      <p className="text-sm text-gray-500 mt-1">{description}</p>
    </div>
    <div className="flex-1 max-w-xl flex flex-col gap-4">
      {children}
    </div>
  </div>
);

export const SettingsView = () => {
  return (
    <motion.div 
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      className="glass-card p-8"
    >
      <SettingSection 
        title="Profile Information" 
        description="Update your personal details and how others see you."
      >
        <div className="grid grid-cols-2 gap-4">
          <div className="flex flex-col gap-1.5">
            <label className="text-xs font-bold text-gray-400 uppercase tracking-wider">First Name</label>
            <input type="text" defaultValue="Nelson" className="px-4 py-2 bg-gray-50 technical-border rounded-xl focus:ring-2 focus:ring-brand-accent/10 focus:border-brand-accent outline-none text-sm" />
          </div>
          <div className="flex flex-col gap-1.5">
            <label className="text-xs font-bold text-gray-400 uppercase tracking-wider">Last Name</label>
            <input type="text" defaultValue="Machicado" className="px-4 py-2 bg-gray-50 technical-border rounded-xl focus:ring-2 focus:ring-brand-accent/10 focus:border-brand-accent outline-none text-sm" />
          </div>
        </div>
        <div className="flex flex-col gap-1.5">
          <label className="text-xs font-bold text-gray-400 uppercase tracking-wider">Email Address</label>
          <input type="email" defaultValue="admin@grancanaveral.com" className="px-4 py-2 bg-gray-50 technical-border rounded-xl focus:ring-2 focus:ring-brand-accent/10 focus:border-brand-accent outline-none text-sm" />
        </div>
      </SettingSection>

      <SettingSection 
        title="System Preferences" 
        description="Configure how the system behaves and looks."
      >
        <div className="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
          <div className="flex items-center gap-3">
            <Moon size={20} className="text-gray-400" />
            <div>
              <p className="text-sm font-bold">Dark Mode</p>
              <p className="text-xs text-gray-500">Enable a darker theme for reduced eye strain.</p>
            </div>
          </div>
          <div className="w-10 h-6 bg-gray-200 rounded-full relative cursor-not-allowed">
            <div className="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all" />
          </div>
        </div>
        <div className="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
          <div className="flex items-center gap-3">
            <Globe size={20} className="text-gray-400" />
            <div>
              <p className="text-sm font-bold">System Language</p>
              <p className="text-xs text-gray-500">Set the default language for the interface.</p>
            </div>
          </div>
          <select className="bg-transparent text-sm font-bold text-brand-accent outline-none">
            <option>Spanish (ES)</option>
            <option>English (US)</option>
          </select>
        </div>
      </SettingSection>

      <div className="flex justify-end mt-8">
        <button className="bg-brand-primary text-white px-8 py-3 rounded-xl font-bold flex items-center gap-2 hover:bg-gray-800 transition-all active:scale-95 shadow-lg shadow-gray-200">
          <Save size={18} />
          Save Changes
        </button>
      </div>
    </motion.div>
  );
};
