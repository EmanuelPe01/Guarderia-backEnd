<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use OpenApi\Annotations as OA;


class RoleController extends Controller
{
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
         *             @OA\Property(property="name", type="string"), 
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
            'name' => 'required|unique:roles'
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Rol creado con éxito', 
            'role' => $role
        ], 201);
    }

     /**
     * Se verifica si el token esta autorizado o no
     *
     * @OA\Get(
     *     path="/api/allRoles",
     *     tags={"Roles"},
     *     summary="Se obtienen todos los roles",
     *     @OA\Response(
     *         response=200,
     *         description="Retorna la informacion de todos los roles"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error"
     *     )
     * )
    */
    public function getAllRoles(){
        $roles = Role::all();
        return response()->json([
            'roles' => $roles
        ], 200);
    }
}
