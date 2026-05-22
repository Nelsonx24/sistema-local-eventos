<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StaffAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $key = 'login_attempts_'.$request->ip();

        if (Cache::get($key, 0) >= 5) {
            Log::warning('Cuenta bloqueada temporalmente por IP', [
                'ip' => $request->ip(),
                'username' => $request->username,
            ]);

            return back()->withErrors([
                'username' => 'Demasiados intentos. Intente nuevamente en 15 minutos.',
            ]);
        }

        $staff = Staff::where('username', $request->username)->first();

        if ($staff && Hash::check($request->password, $staff->password)) {
            Cache::forget($key);
            Auth::guard('staff')->login($staff);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        Cache::put($key, Cache::get($key, 0) + 1, now()->addMinutes(15));

        Log::warning('Intento de inicio de sesión fallido', [
            'username' => $request->username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->withErrors([
            'username' => 'Usuario o contraseña incorrectos.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function authenticated(Request $request, $user)
    {
        if (! $user->canAccess()) {
            Auth::guard('staff')->logout();

            return redirect('/login')->withErrors([
                'username' => 'Este cargo no tiene acceso al sistema.',
            ]);
        }
    }
}
