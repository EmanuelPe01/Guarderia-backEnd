<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Food;
use Exception;
use Illuminate\Http\Request;

class FoodController extends Controller
{
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
