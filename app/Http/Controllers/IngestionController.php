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
    }
}
