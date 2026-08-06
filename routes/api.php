<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    PersonalInfoFieldController,
    MedicalHistoryFieldController,
};

Route::group(['prefix' => 'auth'], function ($route) {
    $route->post('register', [UserController::class, 'register']);
    $route->post('login', [UserController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [UserController::class, 'logout']);

    Route::post('personal-info-fields/reorder', [PersonalInfoFieldController::class, 'reorderForm']);
    Route::put('personal-info-fields/{field}', [PersonalInfoFieldController::class, 'update']);
    Route::delete('personal-info-fields/{field}', [PersonalInfoFieldController::class, 'destroy']);
    Route::apiResource('personal-info-fields', PersonalInfoFieldController::class)->except(['update', 'destroy']);

    Route::post('medical-history-fields/reorder', [MedicalHistoryFieldController::class, 'reorderForm']);
    Route::put('medical-history-fields/{field}', [MedicalHistoryFieldController::class, 'update']);
    Route::delete('medical-history-fields/{field}', [MedicalHistoryFieldController::class, 'destroy']);
    Route::apiResource('medical-history-fields', MedicalHistoryFieldController::class)->except(['update', 'destroy']);
});