<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::orderBy('name')->get();

        return view('staff.index', compact('staff'));
    }

    public function show(Staff $staff)
    {
        return response()->json($staff->only([
            'id', 'name', 'first_name', 'last_name', 'role', 'username', 'email', 'status', 'avatar',
        ]));
    }

    public function edit(Staff $staff)
    {
        return response()->json($staff->only([
            'id', 'name', 'first_name', 'last_name', 'role', 'username', 'email', 'status', 'avatar',
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'role' => 'required|string',
            'username' => 'required|string|unique:staff,username',
            'password' => 'required|string|min:8',
            'status' => 'string',
        ]);

        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);

        $validated['avatar'] = 'https://api.dicebear.com/7.x/avataaars/svg?seed='.str_replace(' ', '', $validated['name']);
        $validated['status'] = $request->status ?? 'Active';

        $staffMember = Staff::create($validated);

        Log::record('Personal', 'Crear', "Trabajador {$staffMember->name} registrado como {$staffMember->role}");

        return redirect()->route('staff.index')->with('success', 'Personal registrado.');
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'email|unique:staff,email,'.$staff->id,
            'role' => 'string',
            'username' => 'nullable|string|unique:staff,username,'.$staff->id,
            'password' => 'nullable|string',
            'status' => 'string',
        ]);

        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);

        if ($request->filled('password')) {
            $validated['password'] = $request->password;
        } else {
            unset($validated['password']);
        }

        $staff->update($validated);

        Log::record('Personal', 'Actualizar', "Trabajador {$staff->name} actualizado");

        return redirect()->route('staff.index')->with('success', 'Personal actualizado.');
    }

    public function changePassword(Request $request, Staff $staff)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $staff->password = $request->password;
        $staff->save();

        Log::record('Personal', 'Actualizar', "Contraseña cambiada para {$staff->name}");

        return redirect()->route('staff.index')->with('success', 'Contraseña actualizada.');
    }

    public function destroy(Staff $staff)
    {
        $name = $staff->name;
        $staff->delete();

        Log::record('Personal', 'Eliminar', "Trabajador {$name} eliminado");

        return redirect()->route('staff.index')->with('success', 'Personal eliminado.');
    }
}
