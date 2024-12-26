<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Catrgory\CatrgoryController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Branch\BranchController;
use App\Http\Controllers\SuperAdmin\SUperAdminController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\ShopInfo\ShopInfoController;
use App\Http\Controllers\Customer\CustomerControlle;

// Supper admin authentication
Route::post('/superadmin/signup', [SUperAdminController::class, 'superadminSignup']);
Route::post('/superadmin/login', [SUperAdminController::class, 'superadminLogin']);

// Supper admin route
Route::middleware('auth:sanctum')->group(function () {
    // Supper admin logout
    Route::post('/superadmin/logout', [SUperAdminController::class, 'logout']);

    // Create Admin
    Route::post('/superadmin/register', [SUperAdminController::class, 'register']);
    // Route to list admins
    Route::get('/superadmin/list', [SUperAdminController::class, 'index']);
    // Edit Admin
    Route::get('/superadmin/edit/{id}', [SUperAdminController::class, 'edit']);
    // Update Admin
    Route::put('/superadmin/update/{id}', [SUperAdminController::class, 'update']);
    // Delete Admin
    Route::delete('/superadmin/delete/{id}', [SUperAdminController::class, 'destroy']);
    // Route for toggling admin status
    Route::put('/superadmin/toggle-status/{id}', [SUperAdminController::class, 'toggleStatus']);

    // Route for adding SMS count
    Route::put('/admin/add-sms-count/{id}', [SUperAdminController::class, 'addSmsCount']);
    // Route for removeing SMS count
    Route::put('/admin/remove-sms-count/{id}', [SUperAdminController::class, 'removeSmsCount']);
});


// Managers routes
// Managers authentication
Route::post('/admin/login', [ManagerController::class, 'adminLogin']);

// Managers routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [ManagerController::class, 'adminlogout']);


    // Category routes
    Route::post('/admin/categories', [CatrgoryController::class, 'categorystore']);
    Route::get('/admin/categories', [CatrgoryController::class, 'showAllCategories']);
    Route::put('/admin/categories/status/{id}', [CatrgoryController::class, 'toggleCategoryStatus']);
    Route::get('/admin/categories/{id}', [CatrgoryController::class, 'editCategory']);
    Route::put('/admin/categories/{id}', [CatrgoryController::class, 'updateCategory']);
    Route::delete('/admin/categories/{id}', [CatrgoryController::class, 'deleteCategory']);

    // Product routes
    Route::post('/admin/products', [ProductController::class, 'createProduct']);
    Route::get('/admin/products', [ProductController::class, 'showAllProducts']);
    Route::get('/admin/products/{id}', [ProductController::class, 'editProduct']);
    Route::put('/admin/products/{id}', [ProductController::class, 'updateProduct']);
    Route::delete('/admin/products/{id}', [ProductController::class, 'deleteProduct']);
    Route::put('/admin/products/status/{id}', [ProductController::class, 'toggleProductStatus']);

    // Product size routes
    Route::post('/sizes/{productId}', [ProductController::class, 'addSize']);
    Route::get('/sizes/edit/{id}', [ProductController::class, 'editSize']);
    Route::put('/sizes/{id}', [ProductController::class, 'updateSize']);
    Route::delete('/sizes/{id}', [ProductController::class, 'deleteSize']);

    // Product extra routes
    Route::post('/extras/{productId}', [ProductController::class, 'addExtra']);
    Route::get('/extras/edit/{id}', [ProductController::class, 'editExtra']);
    Route::put('/extras/{id}', [ProductController::class, 'updateExtra']);
    Route::delete('/extras/{id}', [ProductController::class, 'deleteExtra']);

    // Branch routes
    Route::post('/admin/branches', [BranchController::class, 'createBranch']);
    Route::get('/admin/branches', [BranchController::class, 'showAllBranches']);
    Route::get('/admin/branches/{id}', [BranchController::class, 'editBranch']);
    Route::put('/admin/branches/{id}', [BranchController::class, 'updateBranch']);
    Route::delete('/admin/branches/{id}', [BranchController::class, 'deleteBranch']);

    // staff
    Route::post('/admin/staff', [StaffController::class, 'createStaff']);
    Route::get('/admin/staff', [StaffController::class, 'showAllStaff']);
    Route::get('/admin/staff/{id}', [StaffController::class, 'editStaff']);
    Route::put('/admin/staff/{id}', [StaffController::class, 'updateStaff']);
    Route::put('/admin/staff/status/{id}', [StaffController::class, 'changeStaffStatus']);
    Route::delete('/admin/staff/{id}', [StaffController::class, 'deleteStaff']);

    // shop info routes
    Route::post('/shop-infos', [ShopInfoController::class, 'store']);
    Route::get('/shop-infos', [ShopInfoController::class, 'show']);
    Route::put('/shop-infos', [ShopInfoController::class, 'update']);

    // customer routes
    Route::post('/customers', [CustomerControlle::class, 'store']);
    Route::get('/customers', [CustomerControlle::class, 'index']);
});

// Staff authentication
Route::post('/staff/login', [StaffController::class, 'staffLogin']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/staff/logout', [StaffController::class, 'stafflogout']);
});
