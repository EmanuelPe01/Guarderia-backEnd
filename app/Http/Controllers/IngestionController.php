<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Food;
use App\Models\Ingestion;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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

     /**
     * Retorna la gratificación de los niños, si no tienen ingestiones registradas, no se de devuelven 
     * ni la gratificación ni la hora del tipo de comida.
     * 
     * Se consulta por tipo de comida y por fecha
     *
     * El formato de la fecha es YYYY-MM-DD
     * 
     * @OA\Patch(
     *     path="/api/getIngestasByGroup",
     *     tags={"Ingestiones"},
     *     summary="Publicación de un auncio",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="type", type="string"), 
     *             @OA\Property(property="date", type="string")
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
    public function getIngestasByGroup(Request $request)
    {   
        $user = $request->user();

        if($user->role_id == 1){
            try{
                $request->validate([
                    'type' => 'required',
                    'date' => 'required|date_format:Y-m-d'
                ]);
    
                $groupId = $user->group->id;
    
                $ingestions = DB::table('children')
                    ->leftJoin('ingestions', function ($join){
                        $join->on('children.id', '=', 'ingestions.id_child');
                    })
                    ->leftJoin('food', function ($join) use ($request){
                        $join->on('ingestions.id_food', '=', 'food.id')
                            ->where('food.type', '=', $request->type)
                            ->where('food.date', '=', $request->date);
                    })
                    ->selectRaw('children.id as id_child, food.name, COALESCE(food.hour, "00:00:00") as hour, COALESCE(ingestions.gratification, 0) as gratification')
                    ->where('children.id_group', '=', $groupId)
                    ->get();

                return response()->json([
                    'ingestions' => $ingestions
                ], 200);
            } catch (Exception $ex){
                return response()->json([
                    'message' => $ex
                ]);
            }
        } else {
            return response()->json([
                'message' => 'No autorizado'
            ], 401);
        }
    }

    /**
     * Se verifica si el token esta autorizado o no
     *
     * @OA\Get(
     *     path="/api/getIngestasByChild",
     *     tags={"Ingestiones"},
     *     security={{"bearerAuth": {}}},
     *     summary="Se obtienen las ingestas de los hijos correspondientes al padre",
     *     @OA\Response(
     *         response=200,
     *         description="Retorna las ingestas de los niños registrados"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error"
     *     )
     * )
    */  
    public function getIngestasByChild(Request $request)
    {
        $user = $request->user();

        if($user->role_id == 2){
            $children = $user->children;

            $ingestions = collect();

            foreach ($children as $child) {
                $ingestion = $child->ingestion;
                $ingestion->load('child', 'food');
                $ingestions = $ingestions->concat($ingestion);
            }

            return response()->json([
                'ingestions' => $ingestions
            ], 200);
        } else {
            return response()->json([
                'message' => 'No autorizado',
            ], 401);
        }
    }
}
