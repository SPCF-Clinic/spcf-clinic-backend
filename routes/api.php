<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    PersonalInfoFieldController,
};

Route::group(['prefix' => 'auth'], function ($route) {
    $route->post('register', [UserController::class, 'register']);
    $route->post('login', [UserController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [UserController::class, 'logout']);

    Route::delete('personal-info-fields/{field}', [PersonalInfoFieldController::class, 'destroy']);
    Route::apiResource('personal-info-fields', PersonalInfoFieldController::class)->except(['destroy']);
});