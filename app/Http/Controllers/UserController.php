<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Vamos retornar um Json
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::all();
        return response()->json([
            'status' => true,
            'users' => $users
        ], 200);
    }

    /**
     * Vamos retornar um Json
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'user' => $user
        ], 200);
    }
}
