<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserController,
    PersonalInfoFieldController,
    MedicalHistoryFieldController,
    StudentController,
    EnumController,
    ItemController
};

Route::group(['prefix' => 'auth'], function ($route) {
    $route->post('register', [AuthController::class, 'register']);
    $route->post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('users', [UserController::class, 'index']);

    Route::post('personal-info-fields/reorder', [PersonalInfoFieldController::class, 'reorderForm']);
    Route::put('personal-info-fields/{field}', [PersonalInfoFieldController::class, 'update']);
    Route::delete('personal-info-fields/{field}', [PersonalInfoFieldController::class, 'destroy']);
    Route::apiResource('personal-info-fields', PersonalInfoFieldController::class)->except(['update', 'destroy']);

    Route::post('medical-history-fields/reorder', [MedicalHistoryFieldController::class, 'reorderForm']);
    Route::put('medical-history-fields/{field}', [MedicalHistoryFieldController::class, 'update']);
    Route::delete('medical-history-fields/{field}', [MedicalHistoryFieldController::class, 'destroy']);
    Route::apiResource('medical-history-fields', MedicalHistoryFieldController::class)->except(['update', 'destroy']);

    Route::apiResource('students', StudentController::class)->only(['index', 'show']);

    Route::get('enums/form-field-types', [EnumController::class, 'formFieldTypes']);

    Route::apiResource('items', ItemController::class);
});