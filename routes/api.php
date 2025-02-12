<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserControllerV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// API Route v1
Route::prefix('v1')->group(function () {

    // Retornando informações direto no get
    Route::get('/', function (Request $request) {
        return response()->json([
            'status' => true,
            'message' => 'API/V1'
        ], 200);
    });

    //Middleware Login
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/auth/create', [LoginController::class, 'stores'])->name('login.create');

    Route::group(['middleware' => ['auth:sanctum']], function(){
        Route::post('/auth/logout/{id}', [LoginController::class, 'logout'])->name('login.logout');

        // (Create, Read, Update, Delete)
        Route::get('/users', [UserController::class, 'index'])->name('user.index'); //http://localhost/api/v1/users
        Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');//http://localhost/api/v1/users/id
        Route::resource('users', UserController::class)->except(['index', 'show']);
    });

});

// API Route v2
Route::prefix('v2')->group(function () {
    Route::get('/users', [UserControllerV2::class, 'index']); //http://localhost/api/v2/users?page=2
});

