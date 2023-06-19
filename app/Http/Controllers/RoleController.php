<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use OpenApi\Annotations as OA;


class RoleController extends Controller
{
    public function index()
    {
        
    }
    /**
         * Se almacena un usuario
         *
         * @OA\Post(
         *     path="/api/createRole",
         *     tags={"Roles"},
         *     summary="Creación de un role",
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             @OA\Property(property="nombre", type="string"), 
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Se almacena un role."
         *     ),
         *     @OA\Response(
         *         response="default",
         *         description="Ha ocurrido un error."
         *     )
         * )
         */
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
