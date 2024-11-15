<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Manager\ManagerController;
use App\Http\Controllers\Api\Manager\StaffController;

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

// Admin authentication
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

// Admin routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [AuthController::class, 'adminlogout']);
    // Category routes
    Route::post('/admin/categories', [ManagerController::class, 'categorystore']);
    Route::get('/admin/categories', [ManagerController::class, 'showAllCategories']);
    Route::put('/admin/categories/{id}/status', [ManagerController::class, 'toggleCategoryStatus']);
    Route::get('/admin/categories/{id}', [ManagerController::class, 'editCategory']);
    Route::put('/admin/categories/{id}', [ManagerController::class, 'updateCategory']);
    Route::delete('/admin/categories/{id}', [ManagerController::class, 'deleteCategory']);

    // Product routes
    Route::post('/admin/products', [ManagerController::class, 'createProduct']);
    Route::get('/admin/products', [ManagerController::class, 'showAllProducts']);
    Route::get('/admin/products/{id}', [ManagerController::class, 'editProduct']);
    Route::put('/admin/products/{id}', [ManagerController::class, 'updateProduct']);
    Route::delete('/admin/products/{id}', [ManagerController::class, 'deleteProduct']);
    Route::put('/admin/products/{id}/status', [ManagerController::class, 'toggleProductStatus']);

    // Branch routes
    Route::post('/admin/branches', [ManagerController::class, 'createBranch']);
    Route::get('/admin/branches', [ManagerController::class, 'showAllBranches']);
    Route::get('/admin/branches/{id}', [ManagerController::class, 'editBranch']);
    Route::put('/admin/branches/{id}', [ManagerController::class, 'updateBranch']);
    Route::delete('/admin/branches/{id}', [ManagerController::class, 'deleteBranch']);

    // staff
    Route::post('/admin/staff', [StaffController::class, 'createStaff']);
    Route::get('/admin/staff', [StaffController::class, 'showAllStaff']);
    Route::get('/admin/staff/{id}', [StaffController::class, 'editStaff']);
    Route::put('/admin/staff/{id}', [StaffController::class, 'updateStaff']);
    Route::put('/admin/staff/status/{id}', [StaffController::class, 'changeStaffStatus']);
    Route::delete('/admin/staff/{id}', [StaffController::class, 'deleteStaff']);
});

// Staff authentication
Route::post('/staff/login', [AuthController::class, 'staffLogin']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/staff/logout', [AuthController::class, 'stafflogout']);
});
