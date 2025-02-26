<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }


    /**
     * Vamos retornar um Json
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stores(Request $request): JsonResponse
    {
        try {
            // Cadastrar o usuário no BD
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
            // Retorna os dados do usuário criado e uma mensagem de sucesso com status 201
            return response()->json([
                'user' => $user,
                'message' => "Usuário cadastrado com sucesso!",
            ], 201);

        } catch (QueryException $e) {
            Log::error('Erro no /auth/create: ' . $e->getMessage());
            // Retorna uma mensagem de erro com status 400
            return response()->json([
                'message' => "Usuário não cadastrado!",
            ], 400);
        }
    }

    /**
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try{
            if($token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password])){
                $user = JWTAuth::user();
                return response()->json([
                    'message' => 'Login bem-sucedido',
                    'token_type' => 'Bearer',
                    'token' => $token,
                    'user' => $user,
                ], 200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Credenciais inválidas.',
                ], 401);
            }
        } catch (Exception $e){
            Log::error('Erro no login: ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocorreu um erro ao processar a requisição.',
            ], 500);
        }
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function autenticated(): JsonResponse
    {
        return response()->json([
            'user' => JWTAuth::user(),
        ], 200);

    }
}




