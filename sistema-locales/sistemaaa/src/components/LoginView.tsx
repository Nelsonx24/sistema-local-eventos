import { useState, type FormEvent } from 'react';
import { motion } from 'motion/react';
import { Lock, User, LogIn, LayoutGrid } from 'lucide-react';
import { StaffRole, StaffMember } from '../types';
import { auth } from '../lib/firebase';
import { signInWithPopup, GoogleAuthProvider } from 'firebase/auth';

interface LoginViewProps {
  onLogin: (role: StaffRole, name: string) => void;
  staff: StaffMember[];
}

export const LoginView = ({ onLogin, staff }: LoginViewProps) => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    
    const user = staff.find(s => s.username === username && s.password === password);
    
    if (user) {
      if (user.role === 'Administrador' || user.role === 'Vendedor') {
        const roleMapping: Record<string, StaffRole> = {
          'Administrador': StaffRole.ADMIN,
          'Vendedor': StaffRole.SELLER
        };
        onLogin(roleMapping[user.role], user.name);
      } else {
        setError('Este cargo no tiene acceso al sistema.');
      }
    } else {
      setError('Usuario o contraseña incorrectos.');
    }
  };

  const handleGoogleLogin = async () => {
    try {
      const provider = new GoogleAuthProvider();
      const result = await signInWithPopup(auth, provider);
      if (result.user) {
        // For simplicity in this app, we compare the email with the admin email or staff list
        const registeredStaff = staff.find(s => s.email.toLowerCase() === result.user.email?.toLowerCase());
        
        if (registeredStaff) {
          const roleMapping: Record<string, StaffRole> = {
            'Administrador': StaffRole.ADMIN,
            'Vendedor': StaffRole.SELLER
          };
          onLogin(roleMapping[registeredStaff.role] || StaffRole.SELLER, result.user.displayName || 'Usuario');
        } else if (result.user.email === 'nelsonrmachicado@gmail.com') {
          // Grant admin access to the owner email if not in list
          onLogin(StaffRole.ADMIN, result.user.displayName || 'Admin Nelson');
        } else {
          // If not in staff list, treat as default seller or block?
          // Let's block for security unless it's the specific admin email
          setError('Su correo no está registrado en la nómina del personal.');
        }
      }
    } catch (err: any) {
      setError('Error al iniciar sesión con Google.');
      console.error(err);
    }
  };

  return (
    <div className="min-h-screen bg-[#f8f9fc] flex items-center justify-center p-6 bg-gradient-to-br from-slate-100 to-slate-200">
      <motion.div 
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="w-full max-w-md"
      >
        <div className="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
          <div className="p-8 bg-slate-900 text-white text-center space-y-2">
            <div className="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm shadow-xl">
               <Lock size={32} className="text-white" />
            </div>
            <h1 className="text-2xl font-black tracking-tight">GRAN CAÑAVERAL</h1>
            <p className="text-slate-400 text-sm font-medium">Portal de Gestión Administrativa</p>
          </div>

          <div className="p-10 space-y-8">
            <div className="space-y-4">
              <h2 className="text-xl font-bold text-slate-800">Bienvenido de nuevo</h2>
              <p className="text-slate-500 text-sm">Ingrese sus credenciales para acceder al sistema.</p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-6">
              {error && (
                <div className="p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-xl animate-pulse">
                  {error}
                </div>
              )}

              <div className="space-y-4">
                <div className="relative group">
                  <span className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-brand-accent transition-colors">
                    <User size={18} />
                  </span>
                  <input 
                    type="text" 
                    placeholder="Usuario"
                    className="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all"
                    value={username}
                    onChange={e => setUsername(e.target.value)}
                  />
                </div>

                <div className="relative group">
                  <span className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-brand-accent transition-colors">
                    <Lock size={18} />
                  </span>
                  <input 
                    type="password" 
                    placeholder="Contraseña"
                    className="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all"
                    value={password}
                    onChange={e => setPassword(e.target.value)}
                  />
                </div>
              </div>

              <button 
                type="submit"
                className="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-2 group"
              >
                Acceder al Sistema
                <LogIn size={18} className="group-hover:translate-x-1 transition-transform" />
              </button>

              <div className="relative">
                <div className="absolute inset-0 flex items-center">
                  <span className="w-full border-t border-slate-100"></span>
                </div>
                <div className="relative flex justify-center text-xs uppercase">
                  <span className="bg-white px-4 text-slate-400 font-bold tracking-widest">O acceder con</span>
                </div>
              </div>

              <button 
                type="button"
                onClick={handleGoogleLogin}
                className="w-full bg-white border border-slate-200 text-slate-700 py-3.5 rounded-2xl font-bold hover:bg-slate-50 transition-all flex items-center justify-center gap-3 shadow-sm"
              >
                <img src="https://www.google.com/favicon.ico" alt="Google" className="w-4 h-4" />
                Continuar con Google
              </button>
            </form>
            
            <div className="pt-6 border-t border-slate-100 italic text-[10px] text-slate-400 text-center">
              <p>Módulo de Control de Acceso</p>
            </div>
          </div>
        </div>
        
        <p className="mt-8 text-center text-slate-400 text-xs font-medium">
          Powered by Gran Cañaveral Systems &copy; 2024
        </p>
      </motion.div>
    </div>
  );
};
