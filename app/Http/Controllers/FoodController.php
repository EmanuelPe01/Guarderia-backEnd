<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Exception;
use Illuminate\Http\Request;

class FoodController extends Controller
{
     /**
     * Formato de fecha: YYYY-MM-DD
     * 
     * Formato de hora: HH:MM:SS
     * 
     * @OA\Post(
     *     path="/api/createFood",
     *     tags={"Comidas"},
     *     summary="Registrar comida disponible en un día específico",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"), 
     *             @OA\Property(property="type", type="string"), 
     *             @OA\Property(property="date", type="string"), 
     *             @OA\Property(property="hour", type="string")
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
        try {
            $request->validate([
                'name' => 'required',
                'type' => 'required',
                'date' => 'required|date_format:Y-m-d',
                'hour' => 'required|date_format:H:i:s'
            ]);

            $food = Food::create([
                'name' => $request->name,
                'type' => $request->type,
                'date' => $request->date,
                'hour' => $request->hour
            ]);

            return response()->json([
                'message' => 'Comida registrada correctamente',
                'food' => $food
            ], 201);
        } catch (Exception $e){
            return response()->json([
                'message' => 'No autorizado'
            ], 401);
        }
    }
}
