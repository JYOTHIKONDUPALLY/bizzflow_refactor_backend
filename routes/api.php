<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\CustomerAuthController;
use App\Http\Controllers\V1\Auth\UserAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // Customer Authentication Routes
    Route::prefix('customer')->group(function () {
        Route::post('register', [CustomerAuthController::class, 'register'])->withoutMiddleware('auth:api');
        Route::post('login', [CustomerAuthController::class, 'login']);
        
        Route::middleware('auth:customer')->group(function () {
            Route::post('logout', [CustomerAuthController::class, 'logout']);
            Route::get('me', [CustomerAuthController::class, 'me']);
        });
    });

    // User/Staff Authentication Routes
  Route::prefix('user')->group(function () {
    // Public routes (no auth required)
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);
    
    // All authenticated routes
    Route::middleware('auth:user')->group(function () {
        Route::post('logout', [UserAuthController::class, 'logout']);
        Route::get('me', [UserAuthController::class, 'me']);
        
        // Role-based routes (INSIDE auth middleware)
        Route::middleware(['role:BU Admin'])->group(function () {
            Route::get('/admin/dashboard', function () {
                return response()->json(['message' => 'Welcome to Admin Dashboard']);
            });
            
            Route::get('/admin/users', function () {
                return response()->json(['message' => 'List of users']);
            });
        });
        
        Route::middleware(['role:BU Manager,BU Admin'])->group(function () {
            Route::get('/manager/reports', function () {
                return response()->json(['message' => 'Manager Reports']);
            });
        });
        
        Route::middleware(['role:BU Staff,BU Manager,BU Admin'])->group(function () {
            Route::get('/staff/tasks', function () {
                return response()->json(['message' => 'Staff Tasks']);
            });
        });
    });
});
});