<?php

namespace App\Http\Controllers\V1\Auth;

use App\Domains\Auth\Actions\LoginAction;
use App\Domains\Auth\Actions\LogoutAction;
use App\Domains\Auth\Actions\RegisterCustomerAction;
use App\Domains\Auth\DataTransferObjects\LoginData;
use App\Domains\Auth\DataTransferObjects\RegisterCustomerData;
use App\Domains\Auth\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    public function __construct(
        private RegisterCustomerAction $registerAction,
        private LoginAction $loginAction,
        private LogoutAction $logoutAction
    ) {}

    public function register(RegisterCustomerRequest $request): JsonResponse
    {
        try {
            // Log request data except sensitive info
            Log::info('Register request data: ', $request->except(['password', 'password_confirmation']));

            $validated = $request->validated();
            $data = RegisterCustomerData::from($validated);

            $customer = $this->registerAction->execute($data);

            $authResponse = $this->loginAction->execute(
                new LoginData(
                    email: $customer->email,
                    password: $request->input('password'),
                    franchise_id: $customer->franchise_id,
                    business_unit_id: $customer->business_unit_id
                ),
                UserType::CUSTOMER,
                $request->ip()
            );

            return response()->json([
                'success' => true,
                'message' => 'Customer registered successfully',
                'data' => $authResponse,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error registering customer: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'request' => $request->except(['password', 'password_confirmation']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to register customer. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = LoginData::from($request->validated());

        $authResponse = $this->loginAction->execute(
            $data,
            UserType::CUSTOMER,
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => $authResponse,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Log::info('Customer logout', ['user_id' => $this->getAuthenticatedUser($request)]);
       $user = $this->getAuthenticatedUser($request);
        if ($user) {
            $this->logoutAction->execute($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\Customer $customer */
         $customer = $this->getAuthenticatedUser($request);
        $customer->load(['franchise', 'location', 'country', 'currency']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $customer->id,
                'customer_code' => $customer->customer_code,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'full_name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'status' => $customer->status,
                'franchise' => $customer->franchise ? [
                    'id' => $customer->franchise->id,
                    'name' => $customer->franchise->name,
                    'slug' => $customer->franchise->slug,
                ] : null,
                'location' => $customer->location ? [
                    'id' => $customer->location->id,
                    'name' => $customer->location->name,
                    'code' => $customer->location->code,
                ] : null,
                'country' => $customer->country ? [
                    'id' => $customer->country->id,
                    'name' => $customer->country->name,
                    'code' => $customer->country->code,
                ] : null,
                'currency' => $customer->currency ? [
                    'id' => $customer->currency->id,
                    'name' => $customer->currency->name,
                    'code' => $customer->currency->code,
                    'symbol' => $customer->currency->symbol,
                ] : null,
            ],
        ]);
    }
     private function getAuthenticatedUser(Request $request)
    {
        // Try each guard until we find an authenticated user
        $guards = ['api', 'customer', 'location_admin', 'franchise_admin'];
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }

        // Fallback: try default request user
        
}
}
