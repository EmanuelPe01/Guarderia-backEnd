<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Food;
use App\Models\Ingestion;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IngestionController extends Controller
{
     /**
     * Para poder almacenar un anuncio tiene que existir un profesor que esté a cargo de un grupo.
     * Se verifica que el grupo exista y que además corresponda al usuario que lo esta publicando;
     * se da por hecho que el usuario es un profesor, pues los unicos que pueden ser registrados 
     * en la tabla de grupos, son los profesores.
     *
     * El formato de la fecha es YYYY-MM-DD
     * 
     * @OA\Post(
     *     path="/api/createIngestion",
     *     tags={"Ingestiones"},
     *     summary="Publicación de un auncio",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="gratification", type="integer"), 
     *             @OA\Property(property="id_child", type="integer"), 
     *             @OA\Property(property="id_food", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Se almacena un anuncio en un grupo."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role_id == 1){
            try{
                $request->validate([
                    'user_id' => [
                        Rule::exists(User::class, 'id')->where(function ($query){
                            $query->where('role_id', 1);
                        })
                    ],
                    'gratification'=> 'required',
                    'id_child' => [
                        'required',
                        Rule::exists(Child::class, 'id')->where(function ($query) use ($request){
                            $query->where('id', $request->id_child);
                        })
                    ],
                    'id_food' => [
                        'required',
                        Rule::exists(Food::class, 'id')->where(function ($query) use ($request){
                            $query->where('id', $request->id_food);
                        })
                    ]
                ]);
    
                $ingestion = Ingestion::create([
                    'gratification' => $request->gratification,
                    'id_child' => $request->id_child,
                    'id_food' => $request->id_food
                ]);
    
                return response()->json([
                    'message' => 'Ingestión registrada correctamente',
                    'Ingestion' => $ingestion
                ], 201);
            } catch (Exception $e){
                return response()->json([
                    'message' => 'No autorizado'
                ], 401);
            }
        } else {
            return response()->json([
                'message' => 'No autorizado'
            ], 401);
        }
    }
}
