<?php
namespace App\Http\Controllers\V1\Auth;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Customer\RegisterCustomerAction;
use App\DataTransferObjects\Auth\LoginData;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Customer\RegisterCustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationAdminAuthController extends Controller
{
    public function register(RegisterCustomerRequest $request): JsonResponse
    {
        $data = RegisterCustomerRequest::from($request->
validated());
        
        $admin = $this->registerAction->execute($data);

        $authResponse = $this->loginAction->execute(
            new LoginData(
                email: $admin->email,
                password: $request->password,
                franchise_id: $admin->franchise_id,
                location_id: $admin->location_id
            ),
            UserType::LOCATION_ADMIN,
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Location admin registered successfully',
            'data' => $authResponse,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = LoginData::from($request->validated());

        $authResponse = $this->loginAction->execute(
            $data,
            UserType::LOCATION_ADMIN,
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
        $this->logoutAction->execute($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $admin = $request->user();
        $admin->load(['franchise', 'location']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $admin->id,
                'first_name' => $admin->first_name,
                'last_name' => $admin->last_name,
                'full_name' => $admin->full_name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'status' => $admin->status,
                'franchise' => [
                    'id' => $admin->franchise->id,
                    'name' => $admin->franchise->name,
                ],
                'location' => [
                    'id' => $admin->location->id,
                    'name' => $admin->location->name,
                    'code' => $admin->location->code,
                ],
            ],
        ]);
    }
}