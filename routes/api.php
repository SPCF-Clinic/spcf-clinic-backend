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
    ItemController,
    DispensedItemController,
    BedController,
    CheckInController,
    StudentInfoController,
    DashboardController,
    ActivityLogController,
};

Route::group(['prefix' => 'auth'], function ($route) {
    $route->post('register', [AuthController::class, 'register']);
    $route->post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::get('users', [UserController::class, 'index']);

    Route::post('personal-info-fields/reorder', [PersonalInfoFieldController::class, 'reorderForm']);
    Route::put('personal-info-fields/{field}', [PersonalInfoFieldController::class, 'update']);
    Route::delete('personal-info-fields/{field}', [PersonalInfoFieldController::class, 'destroy']);
    Route::apiResource('personal-info-fields', PersonalInfoFieldController::class)->except(['update', 'destroy']);

    Route::post('medical-history-fields/reorder', [MedicalHistoryFieldController::class, 'reorderForm']);
    Route::put('medical-history-fields/{field}', [MedicalHistoryFieldController::class, 'update']);
    Route::delete('medical-history-fields/{field}', [MedicalHistoryFieldController::class, 'destroy']);
    Route::apiResource('medical-history-fields', MedicalHistoryFieldController::class)->except(['update', 'destroy']);

    Route::get('students/{student}/check-ins', [StudentController::class, 'indexCheckIns']);
    Route::put('students/{student}/personal-info', [StudentInfoController::class, 'updatePersonalInfo']);
    Route::put('students/{student}/medical-history', [StudentInfoController::class, 'updateMedicalHistory']);
    Route::apiResource('students', StudentController::class)->only(['index', 'show']);

    Route::get('enums/form-field-types', [EnumController::class, 'formFieldTypes']);

    Route::apiResource('items', ItemController::class);
    Route::post('dispense-item', [DispensedItemController::class, 'dispenseItem']);

    Route::apiResource('beds', BedController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('check-ins', CheckInController::class)->except(['destroy']);

    Route::apiResource('activity-logs', ActivityLogController::class)->only(['index', 'store']);
});