<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController
};

Route::group(['prefix' => 'auth'], function ($route) {
    $route->post('register', [UserController::class, 'register']);
    $route->post('login', [UserController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [UserController::class, 'logout']);
});