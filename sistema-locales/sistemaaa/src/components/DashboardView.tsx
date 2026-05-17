import { motion } from 'motion/react';
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer,
  AreaChart,
  Area
} from 'recharts';
import { ArrowUpRight } from 'lucide-react';
import { SALON_STATS } from '../mockData';

const CHART_DATA = [
  { name: 'Sem 1', value: 4000 },
  { name: 'Sem 2', value: 3000 },
  { name: 'Sem 3', value: 2000 },
  { name: 'Sem 4', value: 2780 },
];

const StatCard = ({ title, value, trend, isUp }: { title: string, value: string | number, trend: string, isUp: boolean }) => (
  <div className="glass-card p-5">
    <p className="text-[0.75rem] text-text-muted font-bold uppercase tracking-wider mb-2">{title}</p>
    <h3 className="text-2xl font-bold text-brand-primary tracking-tight">{value}</h3>
    <p className={`text-[0.75rem] mt-2 font-medium ${isUp ? 'text-emerald-500' : 'text-red-500'}`}>
      {isUp ? '▲' : '▼'} {trend}
    </p>
  </div>
);

export const DashboardView = () => {
  return (
    <motion.div 
      initial={{ opacity: 0, y: 12 }}
      animate={{ opacity: 1, y: 0 }}
      className="flex flex-col gap-6"
    >
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <StatCard title="Ingresos del Mes" value={`$${SALON_STATS.monthlyRevenue.toLocaleString()}`} trend="12.5% vs mes ant." isUp={true} />
        <StatCard title="Eventos este Mes" value={SALON_STATS.eventsThisMonth} trend="3 nuevos hoy" isUp={true} />
        <StatCard title="Alertas Inventario" value={SALON_STATS.inventoryAlerts} trend="Bajo stock" isUp={false} />
        <StatCard title="Personal Staff" value={SALON_STATS.staffCount} trend="Operativo" isUp={true} />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="glass-card flex flex-col h-full min-h-[400px]">
          <div className="px-6 py-4 border-b border-border-subtle flex justify-between items-center">
            <h3 className="font-semibold text-text-main">Rendimiento de Ventas</h3>
            <button className="text-xs font-bold text-brand-accent hover:underline">Ver reporte</button>
          </div>
          <div className="p-6 flex-1">
            <ResponsiveContainer width="100%" height={300}>
              <AreaChart data={CHART_DATA}>
                <defs>
                  <linearGradient id="colorValue" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#3b82f6" stopOpacity={0.1}/>
                    <stop offset="95%" stopColor="#3b82f6" stopOpacity={0}/>
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 11, fill: '#64748b' }} />
                <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 11, fill: '#64748b' }} />
                <Area type="monotone" dataKey="value" stroke="#3b82f6" strokeWidth={2} fillOpacity={1} fill="url(#colorValue)" />
              </AreaChart>
            </ResponsiveContainer>
          </div>
        </div>

        <div className="glass-card flex flex-col h-full min-h-[400px]">
          <div className="px-6 py-4 border-b border-border-subtle">
            <h3 className="font-semibold text-text-main">Actividad del Sistema</h3>
          </div>
          <div className="p-6 flex-1">
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={CHART_DATA}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 11, fill: '#64748b' }} />
                <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 11, fill: '#64748b' }} />
                <Bar dataKey="value" fill="#0f172a" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>
      </div>
    </motion.div>
  );
};
