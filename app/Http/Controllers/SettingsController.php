<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function update(Request $request)
    {
        // Configuración de perfil y preferencias
        // Por ahora solo retorna éxito ya que la UI original era de muestra
        return back()->with('success', 'Configuración guardada.');
    }
}
