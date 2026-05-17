import { useState, type FormEvent } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Mail, MoreHorizontal, UserCheck, Shield, X, Plus, Trash2, Edit2 } from 'lucide-react';
import { StaffMember } from '../types';

import { addDocument, updateDocument, deleteDocument } from '../services/firebaseService';

interface StaffViewProps {
  staff: StaffMember[];
}

export const StaffView = ({ staff }: StaffViewProps) => {
  const [showModal, setShowModal] = useState(false);
  const [editingStaff, setEditingStaff] = useState<StaffMember | null>(null);
  const [formData, setFormData] = useState({
    name: '',
    role: 'Mesero' as any,
    username: '',
    password: '',
    email: '',
    status: 'Active' as any
  });

  const handleOpenRegister = () => {
    setEditingStaff(null);
    setFormData({
      name: '',
      role: 'Mesero',
      username: '',
      password: '',
      email: '',
      status: 'Active'
    });
    setShowModal(true);
  };

  const handleOpenEdit = (member: StaffMember) => {
    setEditingStaff(member);
    setFormData({
      name: member.name,
      role: member.role,
      username: member.username || '',
      password: member.password || '',
      email: member.email,
      status: member.status
    });
    setShowModal(true);
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    if (editingStaff) {
      const { id, ...data } = editingStaff;
      await updateDocument('staff', id, { ...data, ...formData });
    } else {
      const newMember = {
        ...formData,
        avatar: `https://api.dicebear.com/7.x/avataaars/svg?seed=${formData.name.replace(/\s/g, '')}`
      };
      await addDocument('staff', newMember);
    }
    setShowModal(false);
  };

  const handleDelete = async (id: string) => {
    await deleteDocument('staff', id);
  };

  return (
    <motion.div 
      initial={{ opacity: 0, scale: 0.99 }}
      animate={{ opacity: 1, scale: 1 }}
      className="flex flex-col gap-6"
    >
      <div className="flex justify-between items-center px-6 py-4 bg-white rounded-lg border border-border-subtle shadow-sm overflow-hidden">
        <h3 className="font-semibold text-text-main">Personal y Trabajadores</h3>
        <button 
          onClick={handleOpenRegister}
          className="bg-brand-primary text-white px-4 py-2 rounded-[6px] text-[0.8rem] font-bold hover:bg-slate-800 transition-all flex items-center gap-2"
        >
          <Plus size={16} />
          Registrar Trabajador
        </button>
      </div>

      <div className="glass-card overflow-hidden">
        <table className="w-full border-collapse text-left">
          <thead>
            <tr className="bg-[#f8fafc] border-b border-border-subtle">
              <th className="px-6 py-3 text-[0.75rem] font-bold text-text-muted uppercase tracking-widest">Trabajador</th>
              <th className="px-6 py-3 text-[0.75rem] font-bold text-text-muted uppercase tracking-widest">Cargo / Función</th>
              <th className="px-6 py-3 text-[0.75rem] font-bold text-text-muted uppercase tracking-widest text-center">Estado</th>
              <th className="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody className="divide-y divide-[#f1f5f9]">
            {staff.map((member) => (
              <tr key={member.id} className="hover:bg-[#f8fafc] transition-colors group">
                <td className="px-6 py-4">
                  <div className="flex items-center gap-3">
                    <img src={member.avatar} alt={member.name} className="w-10 h-10 rounded-full border border-border-subtle shadow-sm" />
                    <div>
                      <p className="font-bold text-[0.875rem] text-text-main">{member.name}</p>
                      <p className="text-[0.75rem] text-text-muted">{member.email}</p>
                    </div>
                  </div>
                </td>
                <td className="px-6 py-4">
                  <div className="flex items-center gap-2 text-[0.875rem] text-text-main">
                    <Shield size={14} className="text-brand-accent" />
                    {member.role}
                  </div>
                </td>
                <td className="px-6 py-4 text-center">
                  <span className={`status-pill inline-flex items-center gap-1.5 ${
                    member.status === 'Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-500 border-slate-200'
                  }`}>
                    <span className={`w-1.5 h-1.5 rounded-full ${member.status === 'Active' ? 'bg-emerald-500' : 'bg-slate-400'}`} />
                    {member.status === 'Active' ? 'Activo' : 'Inactivo'}
                  </span>
                </td>
                <td className="px-6 py-4 text-right">
                  <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button 
                      onClick={() => handleOpenEdit(member)}
                      className="p-2 text-text-muted hover:text-brand-accent transition-colors"
                      title="Editar"
                    >
                      <Edit2 size={16} />
                    </button>
                    <button 
                      onClick={() => handleDelete(member.id)}
                      className="p-2 text-text-muted hover:text-red-500 transition-colors"
                      title="Eliminar"
                    >
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
            {staff.length === 0 && (
              <tr>
                <td colSpan={4} className="px-6 py-12 text-center text-slate-400 italic">
                  No hay personal registrado en el sistema.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      <AnimatePresence>
        {showModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <motion.div 
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              className="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-border-subtle"
            >
              <div className="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
                <h3 className="font-bold text-text-main">
                  {editingStaff ? 'Editar Trabajador' : 'Registrar Trabajador'}
                </h3>
                <button onClick={() => setShowModal(false)} className="text-text-muted hover:text-text-main transition-colors">
                  <X size={20} />
                </button>
              </div>
              <form onSubmit={handleSubmit} className="p-6 flex flex-col gap-4 text-left">
                <div className="flex flex-col gap-1.5">
                  <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Nombre Completo</label>
                  <input 
                    required 
                    type="text" 
                    className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none focus:ring-1 focus:ring-brand-accent"
                    value={formData.name}
                    onChange={e => setFormData({...formData, name: e.target.value})}
                  />
                </div>
                <div className="flex flex-col gap-1.5">
                  <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Correo Electrónico</label>
                  <input 
                    required 
                    type="email" 
                    className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none focus:ring-1 focus:ring-brand-accent"
                    value={formData.email}
                    onChange={e => setFormData({...formData, email: e.target.value})}
                  />
                </div>
                {(formData.role === 'Administrador' || formData.role === 'Vendedor') && (
                  <div className="grid grid-cols-2 gap-4">
                    <div className="flex flex-col gap-1.5">
                      <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Username (Acceso)</label>
                      <input 
                        required 
                        type="text" 
                        className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none focus:ring-1 focus:ring-brand-accent"
                        value={formData.username}
                        onChange={e => setFormData({...formData, username: e.target.value})}
                      />
                    </div>
                    <div className="flex flex-col gap-1.5">
                      <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Password</label>
                      <input 
                        required 
                        type="text" 
                        className="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none focus:ring-1 focus:ring-brand-accent"
                        value={formData.password}
                        onChange={e => setFormData({...formData, password: e.target.value})}
                      />
                    </div>
                  </div>
                )}
                <div className="grid grid-cols-2 gap-4">
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Cargo / Función</label>
                    <select 
                      className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none"
                      value={formData.role}
                      onChange={e => setFormData({...formData, role: e.target.value as any})}
                    >
                      <option value="Administrador">Administrador</option>
                      <option value="Vendedor">Vendedor</option>
                      <option value="Mesero">Mesero</option>
                      <option value="Seguridad">Seguridad</option>
                      <option value="Limpieza">Limpieza</option>
                      <option value="Catering">Catering</option>
                    </select>
                  </div>
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Estado</label>
                    <select 
                      className="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none"
                      value={formData.status}
                      onChange={e => setFormData({...formData, status: e.target.value as any})}
                    >
                      <option value="Active">Activo</option>
                      <option value="On Leave">Inactivo</option>
                    </select>
                  </div>
                </div>
                <button type="submit" className="mt-4 bg-brand-primary text-white py-2.5 rounded-lg font-bold hover:bg-slate-800 transition-all shadow-md">
                  {editingStaff ? 'Actualizar Información' : 'Registrar Trabajador'}
                </button>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </motion.div>
  );
};
