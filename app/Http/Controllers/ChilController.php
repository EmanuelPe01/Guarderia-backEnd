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
                'message' => 'No autorizado'
            ], 401);
        }
    }

}
