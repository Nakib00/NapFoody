<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AdminController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Supper admin authentication
Route::post('/superadmin/signup', [AuthController::class, 'superadminSignup']);
Route::post('/superadmin/login', [AuthController::class, 'superadminLogin']);

// Supper admin route
Route::middleware('auth:sanctum')->group(function () {
    // Supper admin logout
    Route::post('/superadmin/logout', [AuthController::class, 'logout']);
    // Route to list admins
    Route::get('/admin/list', [AdminController::class, 'index']);
    // Create Admin
    Route::post('/admin/register', [AdminController::class, 'register']);
    // Edit Admin
    Route::get('admin/edit/{id}', [AdminController::class, 'edit']);
    // Update Admin
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    // Delete Admin
    Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
    // Route for toggling admin status
    Route::put('/admin/{id}/toggle-status', [AdminController::class, 'toggleStatus']);
    // Route for adding SMS count
    Route::put('/admin/{id}/add-sms-count', [AdminController::class, 'addSmsCount']);
    // Route for removeing SMS count
    Route::put('/admin/{id}/remove-sms-count', [AdminController::class, 'removeSmsCount']);
});
