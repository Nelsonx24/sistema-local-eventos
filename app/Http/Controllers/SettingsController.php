<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Log;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_phone' => 'nullable|string|max:20',
        ]);

        if ($request->filled('site_name')) {
            Config::set('site_name', $request->site_name);
        }

        if ($request->filled('site_phone')) {
            Config::set('site_phone', $request->site_phone);
        }

        Log::record('Configuración', 'Actualizar', 'Configuración general actualizada');

        return back()->with('success', 'Configuración guardada.');
    }
}
