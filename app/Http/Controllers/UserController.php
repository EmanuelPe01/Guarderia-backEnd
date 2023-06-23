<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Api para los usuarios",
 *      description="Se implementaron pocos metodos"
 * )
 */

class UserController extends Controller
{
        /**
         * Se almacena un usuario
         *
         * @OA\Post(
         *     path="/api/createUser",
         *     tags={"Users"},
         *     summary="Creación de un usuario",
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             @OA\Property(property="name", type="string"), 
         *             @OA\Property(property="email", type="string"),
         *             @OA\Property(property="telephone", type="string"), 
         *             @OA\Property(property="password", type="string"),
         *             @OA\Property(property="role_id", type="integer"),
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Se almacena un usuario."
         *     ),
         *     @OA\Response(
         *         response="default",
         *         description="Ha ocurrido un error."
         *     )
         * )
         */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'telephone' => 'required|unique:users',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'role_id' => $request->role_id,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Usuario registrado',
            'usuario' => $user
        ], 201);
    }

    /**
     * El usuario inicia sesión
     *
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Users"},
     *     summary="Usuario inicia sesión",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un token para el usuario."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Credenciales inválidas."
     *     )
     * )
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            //El plugin PHP Intelephense marca como error la funcion createToken, pero hay que hacer caso omiso
            $token = $user->createToken('token')->plainTextToken;
    
            return response()->json(['token' => $token ,'user'=>$user], 200);
        }
    
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    /**
     * Logout de usuario
     * Es necesario agregar el Token en el candado que aparece en el encabezado, 
     * una vez agregado, dar clic en authorize y después se ejecuta el método
     *
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Users"},
     *     summary="Cerrar sesión",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cierre de sesión exitoso."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     *
     * @OA\SecurityScheme(
     *     type="http",
     *     securityScheme="bearerAuth",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * )
     */

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }

    /**
         * Se verifica si el token esta autorizado o no
         *
         * @OA\Get(
         *     path="/api/check-status",
         *     tags={"Users"},
         *     summary="Verificacion de token",
         *     security={{"bearerAuth": {}}},
         *     @OA\Response(
         *         response=200,
         *         description="Retorna el usuario y el token"
         *     ),
         *     @OA\Response(
         *         response=401,
         *         description="No autorizado"
         *     )
         * )
         */
    public function checkStatus(Request $request)
    {
        $user = $request->user();
        if($user){
            return response()->json(['token'=>str_replace('Bearer ', '', $request->header('authorization')), 'user'=>$user],200);
        }
    }

    /**
     * Se verifica si el token esta autorizado o no
     *
     * @OA\Get(
     *     path="/api/allUsers",
     *     tags={"Users"},
     *     summary="Se obtienen todos los usuarios",
     *     @OA\Response(
     *         response=200,
     *         description="Retorna la informacion de todos los usuarios"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error"
     *     )
     * )
    */
    public function getAllUsers(){
        $users = User::all();
        return response()->json([
            'users' => $users
        ], 200);
    }
}
