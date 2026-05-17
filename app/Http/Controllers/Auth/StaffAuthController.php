<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $staff = Staff::where('username', $request->username)->first();

        if ($staff && ($staff->password === $request->password || Hash::check($request->password, $staff->password))) {
            Auth::guard('staff')->login($staff);
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

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
        if (!$user->canAccess()) {
            Auth::guard('staff')->logout();
            return redirect('/login')->withErrors([
                'username' => 'Este cargo no tiene acceso al sistema.',
            ]);
        }
    }
}