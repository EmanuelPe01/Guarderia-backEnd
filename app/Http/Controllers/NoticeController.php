<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Notice;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;



class NoticeController extends Controller
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
     *     path="/api/createNotice",
     *     tags={"Anuncios"},
     *     summary="Publicación de un auncio",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_user", type="integer"), 
     *             @OA\Property(property="importance", type="integer"), 
     *             @OA\Property(property="date", type="string"), 
     *             @OA\Property(property="title", type="string"), 
     *             @OA\Property(property="body", type="string"), 
     *             @OA\Property(property="id_group", type="integer"), 
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
        try{
            $request->validate([
                'importance' => 'required|between:0,3',
                'date' => 'required|date_format:Y-m-d',
                'title' => 'required',
                'body' => 'required',
                'id_group' => [
                    'required',
                    Rule::exists(Group::class, 'id')->where(function ($query) use ($request) {
                        $query->where('id', $request->id_group)
                            ->where('id_user', $request->id_user);
                    }),
                ]
            ]);

            $notice = Notice::create([
                'date' => $request->date,
                'importance' => $request->importance, 
                'title' => $request->title,
                'body'=> $request->body,
                'id_group' => $request->id_group
            ]);

            return response()->json([
                'message' => 'Anuncio publicado correctamente',
                'notice' => $notice
            ], 201);
        } catch (Exception $e){
            return response()->json([
                'message' => 'No autorizado',
                'error' => $e
            ],400);
        }
    }

    /**
     * Se verifica si el token esta autorizado o no
     *
     * @OA\Get(
     *     path="/api/allNotices",
     *     tags={"Anuncios"},
     *     summary="Se obtienen todos los anuncios",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna la informacion de todos los anuncios"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error"
     *     )
     * )
    */
    public function getAllNotices(){
        $notices = Notice::all();
        return response()->json([
            'notices' => $notices
        ], 200);
    }
}
