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
Route::get('/home', function () {
    return response('Hello World', 200)
        ->header('Content-Type', 'text/plain');
});

Route::prefix('v1')->group(function () {
    
    // Customer Authentication Routes
    Route::prefix('customer')->group(function () {
        Route::get('/home', function () {
    return response('Hello World', 200)
        ->header('Content-Type', 'text/plain');
});
// Route::post('register', function (Request $request) {
//     try {
//         // Log request data except sensitive info
//         Log::info('Register request data: ', $request->except(['password', 'password_confirmation']));

//         $data = $request->validate([
//             // add your validation rules here
//             'email' => 'required|email',
//             'password' => 'required|min:6|confirmed',
//             // other fields...
//         ]);

//         $customer = $this->registerAction->execute($data);

//         $authResponse = $this->loginAction->execute(
//             new LoginData(
//                 email: $customer->email,
//                 password: $request->input('password'),
//                 franchise_id: $customer->franchise_id,
//                 business_unit_id: $customer->business_unit_id
//             ),
//             UserType::CUSTOMER,
//             $request->ip()
//         );

//         return response()->json([
//             'success' => true,
//             'message' => 'Customer registered successfully',
//             'data' => $authResponse,
//         ], 201);

//     } catch (\Exception $e) {
//         Log::error('Error registering customer: ' . $e->getMessage(), [
//             'stack' => $e->getTraceAsString(),
//             'request' => $request->except(['password', 'password_confirmation']),
//         ]);

//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to register customer. Please try again.',
//             'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
//         ], 500);
//     }
// });
        Route::post('register', [CustomerAuthController::class, 'register'])->withoutMiddleware('auth:api');
        Route::post('login', [CustomerAuthController::class, 'login']);
        
        Route::middleware('auth:api')->group(function () {
            Route::post('logout', [CustomerAuthController::class, 'logout']);
            Route::get('me', [CustomerAuthController::class, 'me']);
        });
    });

    // User/Staff Authentication Routes
    // Route::prefix('user')->group(function () {
    //     Route::post('register', [UserAuthController::class, 'register']);
    //     Route::post('login', [UserAuthController::class, 'login']);
        
    //     Route::middleware('auth:sanctum')->group(function () {
    //         Route::post('logout', [UserAuthController::class, 'logout']);
    //         Route::get('me', [UserAuthController::class, 'me']);
    //     });
    // });

    // Location Admin Authentication Routes
    // Route::prefix('location-admin')->group(function () {
    //     Route::post('register', [LocationAdminAuthController::class, 'register']);
    //     Route::post('login', [LocationAdminAuthController::class, 'login']);
        
    //     Route::middleware('auth:sanctum')->group(function () {
    //         Route::post('logout', [LocationAdminAuthController::class, 'logout']);
    //         Route::get('me', [LocationAdminAuthController::class, 'me']);
    //     });
    // });

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
});