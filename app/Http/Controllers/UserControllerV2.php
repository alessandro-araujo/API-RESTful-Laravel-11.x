<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;

class UserControllerV2 extends Controller
{
    /**
     * returnJson
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // http://localhost/api/v2/users?page=1
        $users = User::orderBy('id', 'DESC')->paginate(2);
        return response()->json([
            'status' => true,
            'users' => $users
        ], 200);
    }
}
