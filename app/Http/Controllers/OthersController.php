<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Config;
use App\Models\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OthersController extends Controller
{
    public function index()
    {
        return view('others.menu');
    }

    public function qr()
    {
        $qrImage = Config::getQR();

        return view('others.qr', compact('qrImage'));
    }

    public function updateQr(Request $request)
    {
        $request->validate([
            'qr_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('qr_image')) {
            $path = $request->file('qr_image')->store('qr', 'public');
            Config::setQR(asset('storage/'.$path));
        } else {
            Config::setQR($request->qr_url ?? Config::getQR());
        }

        Log::record('Configuración', 'Actualizar', 'Código QR actualizado');

        return back()->with('success', 'QR actualizado.');
    }

    public function assets()
    {
        $assets = Asset::orderBy('category')->orderBy('name')->get();

        return view('others.assets', compact('assets'));
    }

    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|string',
        ]);

        Asset::create($validated);

        Log::record('Inventario', 'Crear', "Activo {$validated['name']} registrado");

        return back()->with('success', 'Activo registrado.');
    }

    public function destroyAsset(Asset $asset)
    {
        $name = $asset->name;
        $asset->delete();

        Log::record('Inventario', 'Eliminar', "Activo {$name} eliminado");

        return back()->with('success', 'Activo eliminado.');
    }

    public function contractSettings()
    {
        $settings = Config::getContractSettings();

        return view('others.contract-settings', compact('settings'));
    }

    public function updateContractSettings(Request $request)
    {
        $validated = $request->validate([
            'salon_name' => 'required|string',
            'representative' => 'required|string',
            'representative_ci' => 'required|string',
            'city' => 'required|string',
            'watermark' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        Config::setContractSettings($validated);

        if ($request->hasFile('watermark')) {
            $path = $request->file('watermark')->store('watermarks', 'public');
            Config::setWatermark($path);
        }

        Log::record('Configuración', 'Actualizar', 'Configuración de contrato actualizada');

        return back()->with('success', 'Configuración de contrato guardada.');
    }

    public function notifications()
    {
        return view('others.notifications');
    }

    public function downloadAssetsPdf()
    {
        $assets = Asset::orderBy('category')->orderBy('name')->get();

        $html = view('pdf.assets', compact('assets'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Inventario_Activos_'.date('Y-m-d').'.pdf');
    }
}
