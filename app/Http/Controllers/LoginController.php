<?php

namespace App\Http\Controllers;

# use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            if(Auth::attempt(['email' => $request['email'], 'password' => $request['password']])){
                $user = Auth::user();
                $token = $request->user()->createToken('api-token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'token' => $token,
                    'user' => $user,
                ], 200);

            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Login ou senha incorreta.',
                ], 404);
            }
        }catch (\Exception $e) {
            Log::error('Erro no login: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Ocorreu um erro ao processar a requisição.',
            ], 500);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try{
            $request->user()->currentAccessToken()->delete();
            # $user->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Deslogado com sucesso.',
            ], 200);

        } catch (Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Não deslogado.',
            ], 400);
        }
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
                'status' => true,
                'user' => $user,
                'message' => "Usuário cadastrado com sucesso!",
            ], 201);

        } catch (QueryException $e) {
            Log::error('Erro no /auth/create: ' . $e->getMessage());
            // Retorna uma mensagem de erro com status 400
            return response()->json([
                'status' => false,
                'message' => "Usuário não cadastrado!",
            ], 400);
        }
    }
}
