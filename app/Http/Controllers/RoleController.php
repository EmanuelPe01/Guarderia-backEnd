<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:roles'
        ]);

        $role = Role::create([
            'nombre' => $request->nombre,
        ]);

        return response()->json([
            'message' => 'Rol creado con éxito', 
            'role' => $role
        ], 201);
    }

    public function show($id)
    {
        
    }

    public function update(Request $request, $id)
    {
        
    }

    public function destroy($id)
    {
        
    }
}
