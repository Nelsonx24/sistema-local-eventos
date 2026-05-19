<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::orderBy('name')->get();

        return view('staff.index', compact('staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'role' => 'required|string',
            'username' => 'nullable|string|unique:staff,username',
            'password' => 'nullable|string',
            'status' => 'string',
        ]);

        if (in_array($validated['role'], ['Administrador', 'Vendedor'])) {
            $validated['username'] = $request->username;
            $validated['password'] = $request->password;
        }

        $validated['avatar'] = 'https://api.dicebear.com/7.x/avataaars/svg?seed='.str_replace(' ', '', $validated['name']);
        $validated['status'] = $request->status ?? 'Active';

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Personal registrado.');
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:staff,email,'.$staff->id,
            'role' => 'string',
            'username' => 'nullable|string|unique:staff,username,'.$staff->id,
            'password' => 'nullable|string',
            'status' => 'string',
        ]);

        if (in_array($validated['role'], ['Administrador', 'Vendedor'])) {
            if ($request->password) {
                $validated['password'] = $request->password;
            }
        } else {
            unset($validated['username'], $validated['password']);
        }

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Personal actualizado.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Personal eliminado.');
    }
}
