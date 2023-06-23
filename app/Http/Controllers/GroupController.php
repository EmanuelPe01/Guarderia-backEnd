<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
     /**
     * El usuario al que se desea asignar un grupo, debe tener el rol de profesor
     * 
     * @OA\Post(
     *     path="/api/createGroup",
     *     tags={"Grupos"},
     *     summary="Creacion de un grupo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_user", type="integer"), 
     *             @OA\Property(property="name", type="string"), 
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Se almacena un grupo asociado a un profesor."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function store(Request $request)
    {
        try{
            $request->validate([
                'id_user' => [
                    'required',
                    Rule::exists(User::class, 'id')->where(function ($query){
                        $query->where('role_id', 1);
                    })
                ],
                'name' => 'required'
            ]);
    
            $group = Group::create([
                'id_user' => $request->input('id_user'),
                'name' => $request->input('name')
            ]);
    
            return response()->json([
                'message' => 'Grupo registrado correctamente',
                'grupo' => $group
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'message' => 'Usuario no autorizado',
            ], 401);
        }
    }

    /**
     *
     * @OA\Get(
     *     path="/api/allGroups",
     *     tags={"Grupos"},
     *     summary="Se obtienen todos los grupos",
     *     @OA\Response(
     *         response=200,
     *         description="Retorna la informacion de todos los grupos"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error"
     *     )
     * )
    */
    public function getAllGroups(){
        $groups = Group::all();
        return response()->json([
            'groups' => $groups
        ], 200);
    }

    /**
     * Se verifica si el token esta autorizado o no
     *
     * @OA\Get(
     *     path="/api/getChidrenByGroup",
     *     tags={"Grupos"},
     *     security={{"bearerAuth": {}}},
     *     summary="Se obtienen los niños correspondientes al grupo del profesor",
     *     @OA\Response(
     *         response=200,
     *         description="Retorna la informacion de los niños registrados"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error"
     *     )
     * )
    */    
    public function getChildByGroup(Request $request)
    {
        $user = $request->user();

        if($user->role_id == 1){
            $group = $user->group;
            $children = $group->children;

            return response()->json([
                'group' => $children
            ],200);
        } else {
            return response()->json([
                'message' => 'No autorizado'
            ], 401);
        }
    }
}
