<?php

namespace App\Http\Controllers\V1\Auth;

use App\Domains\Auth\Actions\LoginAction;
use App\Domains\Auth\Actions\LogoutAction;
use App\Domains\Auth\DataTransferObjects\LoginData;
use App\Domains\Auth\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Domains\Auth\Actions\RegisterUserAction;
use App\Domains\Auth\DataTransferObjects\RegisterUserData;
class UserAuthController extends Controller
{

    public function __construct(
        private RegisterUserAction $registerAction,
        private LoginAction $loginAction,
        private LogoutAction $logoutAction
    ){}
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = RegisterUserData::from($request->validated());
        
        $user = $this->registerAction->execute($data);

        $authResponse = $this->loginAction->execute(
            new LoginData(
                email: $user->email,
                password: $request->password,
                franchise_id: $user->franchise_id,
                location_id: $user->location_id
            ),
            UserType::USER,
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => $authResponse,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = LoginData::from($request->validated());

        $authResponse = $this->loginAction->execute(
            $data,
            UserType::USER,
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
        $user = $request->user();
        $user->load(['franchise', 'location', 'role']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'franchise' => [
                    'id' => $user->franchise->id,
                    'name' => $user->franchise->name,
                ],
                'location' => [
                    'id' => $user->location->id,
                    'name' => $user->location->name,
                    'code' => $user->location->code,
                ],
                'role' => [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'slug' => $user->role->slug,
                    'permissions' => $user->role->permissions,
                ],
            ],
        ]);
    }
}