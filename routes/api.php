<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\CustomerAuthController;
use App\Http\Controllers\V1\Auth\UserAuthController;
use App\Http\Controllers\V1\Auth\LocationAdminAuthController;
use App\Http\Controllers\V1\Auth\FranchiseAdminAuthController;


use App\Domains\Auth\Actions\LoginAction;
use App\Domains\Auth\Actions\LogoutAction;
use App\Domains\Auth\Actions\RegisterCustomerAction;
use App\Domains\Auth\DataTransferObjects\LoginData;
use App\Domains\Auth\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



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
        
        Route::middleware('auth:api_customer')->group(function () {
            Route::post('logout', [CustomerAuthController::class, 'logout']);
            Route::get('me', [CustomerAuthController::class, 'me']);
        });
    });

    // User/Staff Authentication Routes
    Route::prefix('user')->group(function () {
        Route::post('register', [UserAuthController::class, 'register']);
        Route::post('login', [UserAuthController::class, 'login']);
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [UserAuthController::class, 'logout']);
            Route::get('me', [UserAuthController::class, 'me']);
        });
    });

    // Location Admin Authentication Routes
    Route::prefix('location-admin')->group(function () {
        Route::post('register', [LocationAdminAuthController::class, 'register']);
        Route::post('login', [LocationAdminAuthController::class, 'login']);
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [LocationAdminAuthController::class, 'logout']);
            Route::get('me', [LocationAdminAuthController::class, 'me']);
        });
    });

    // Franchise Admin Authentication Routes
    // Route::prefix('franchise-admin')->group(function () {
    //     Route::post('register', [FranchiseAdminAuthController::class, 'register']);
    //     Route::post('login', [FranchiseAdminAuthController::class, 'login']);
        
    //     Route::middleware('auth:sanctum')->group(function () {
    //         Route::post('logout', [FranchiseAdminAuthController::class, 'logout']);
    //         Route::get('me', [FranchiseAdminAuthController::class, 'me']);
    //         Route::post('switch-location', [FranchiseAdminAuthController::class, 'switchLocation']);
    //     });
    // });



    // Public routes
Route::post('/auth/login', [UserAuthController::class, 'login'])->name('auth.login');

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [UserAuthController::class, 'logout'])->name('auth.logout');
    Route::get('/auth/me', [UserAuthController::class, 'me'])->name('auth.me');
    
    // Example routes with role middleware
    Route::middleware(['role:BUAdmin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Welcome to Admin Dashboard']);
        });
        
        Route::get('/admin/users', function () {
            return response()->json(['message' => 'List of users']);
        });
    });
    
    Route::middleware(['role:BUManager,BUAdmin'])->group(function () {
        Route::get('/manager/reports', function () {
            return response()->json(['message' => 'Manager Reports']);
        });
    });
    
    Route::middleware(['role:BUStaff,BUManager,BUAdmin'])->group(function () {
        Route::get('/staff/tasks', function () {
            return response()->json(['message' => 'Staff Tasks']);
        });
    });
});
});