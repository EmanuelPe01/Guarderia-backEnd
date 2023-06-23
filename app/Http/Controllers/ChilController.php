<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Group;
use App\Models\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChilController extends Controller
{
     /**
     * El id_user debe corresponder a un usuario con el rol de padre, 
     * y el id_group debe existir
     * 
     * @OA\Post(
     *     path="/api/createChild",
     *     tags={"Chamacos"},
     *     summary="Publicación de un auncio",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"), 
     *             @OA\Property(property="first_surname", type="string"), 
     *             @OA\Property(property="second_surname", type="string"), 
     *             @OA\Property(property="id_group", type="integer"), 
     *             @OA\Property(property="id_user", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Se retornan los anuncios según el usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'first_surname' => 'required',
                'second_surname' => 'required',
                'id_group' => [
                    'required',
                    Rule::exists(Group::class, 'id')->where(function ($query) use ($request) {
                        $query->where('id', $request->id_group);
                    }),
                ],
                'id_user' => [
                    'required',
                    Rule::exists(User::class, 'id')->where(function ($query){
                        $query->where('role_id', 2);
                    })
                ]
            ]);
            
            $child = Child::create([
                'name' => $request->name,
                'first_surname' => $request->first_surname,
                'second_surname' => $request->second_surname,
                'id_group' => $request->id_group,
                'id_user' => $request->id_user,
            ]);

            return response()->json([
                'message' => 'Niño registrado correctamente',
                'child' => $child
            ], 201);

        } catch (Exception $e){
            return response()->json([
                'message' => 'No autorizado',
                'error' => $e
            ], 401);
        }
    }

}
