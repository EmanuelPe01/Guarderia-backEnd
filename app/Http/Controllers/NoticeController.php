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
     *             @OA\Property(property="importance", type="integer"), 
     *             @OA\Property(property="date", type="string"), 
     *             @OA\Property(property="title", type="string"), 
     *             @OA\Property(property="body", type="string"),  
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
        $groupID = $request->user()->group->id;
        if ($groupID){
            try{
                $request->validate([
                    'importance' => 'required|between:1,4',
                    'date' => 'required|date_format:Y-m-d',
                    'title' => 'required',
                    'body' => 'required'
                ]);
    
                $notice = Notice::create([
                    'date' => $request->date,
                    'importance' => $request->importance, 
                    'title' => $request->title,
                    'body'=> $request->body,
                    'id_group' => $groupID
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
        } else {
            return response()->json([
                'message' => 'No autorizado'
            ], 401);
        }
    }

     /**
     * Se debe mandar el token de inicio de sesión para verificar que anuncis se devolverán.
     * 
     * Si es profesor se mandan los anuncios correspondientes al grupo, por otro lado, si es 
     * padre, se mandan los anuncios de sus hijos.
     *
     * 
     * @OA\Get(
     *     path="/api/allNotices",
     *     tags={"Anuncios"},
     *     summary="Publicación de un auncio",
     *     security={{"bearerAuth": {}}},
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
    public function getNotices(Request $request)
    {
        $user = $request->user();

        if($user->role_id == 1)
        {
            $group = $user->group;
            $notices = $group->notices;
        } else if($user->role_id == 2)
        {
            $children = $user->children;
            $notices = collect();

            foreach ($children as $child) {
                $group = $child->group;
                $childNotices = $group->notices;
                $notices = $notices->concat($childNotices);
            }
        }

        return response()->json([
            'notices' => $notices
        ], 200);
    }
}
