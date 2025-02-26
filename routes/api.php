<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserControllerV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;

Route::middleware('auth:api')->get('/nginx', function () {
    return response()->json(['version' => 'nginx/1.27.4']);
});


// API Route v2 Routs Public
Route::prefix('v2')->group(function () {
    //Middleware Login
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/auth/create', [AuthController::class, 'stores'])->name('login.create');
});


// API Route v2 - Routs Private
Route::middleware('auth:api')->prefix('v2')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('login.logout');


    Route::get('/routes', function () {
        $routes = collect(app('router')->getRoutes())->map(function ($route) {
            return [
                'uri' => $route->uri(),
                'method' => $route->methods(),
                'name' => $route->getName(),
            ];
        });

        return $routes;
    });

    // (Create, Read, Update, Delete)
    Route::get('/users', [UserController::class, 'index'])->name('user.index'); //http://localhost/api/v1/users
    Route::get('/users/page/{id}', [UserControllerV2::class, 'index']); //http://localhost/api/v2/users?page=2
    Route::get('/users/{id}', [UserController::class, 'show'])->name('user.show');//http://localhost/api/v1/users/id
    Route::resource('users', UserController::class)->except(['index', 'show']);
    Route::get('/autenticated', [AuthController::class, 'autenticated']);
});


// API Route v1 - Routs Public
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
