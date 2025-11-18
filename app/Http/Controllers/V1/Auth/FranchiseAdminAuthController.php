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

class FranchiseAdminAuthController extends Controller
{
    public function __construct(
        private RegisterCustomerAction $registerAction,
        private LoginAction $loginAction,
        private LogoutAction $logoutAction
    ) {}

    public function register(RegisterCustomerRequest $request): JsonResponse
    {
        $data = $request->validated();
         $admin = $this->registerAction->execute($data);

        $authResponse = $this->loginAction->execute(
            new LoginData(
                email: $admin->email,
                password: $request->password,
                franchise_id: $admin->franchise_id
            ),
            UserType::FRANCHISE_ADMIN,
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Franchise admin registered successfully',
            'data' => $authResponse,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = LoginData::from($request->validated());

        $authResponse = $this->loginAction->execute(
            $data,
            UserType::FRANCHISE_ADMIN,
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
        $admin->load(['franchise', 'lastLoginLocation']);

        $accessibleLocations = $admin->getAccessibleLocations();

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
                    'slug' => $admin->franchise->slug,
                ],
                'last_login_location' => $admin->lastLoginLocation ? [
                    'id' => $admin->lastLoginLocation->id,
                    'name' => $admin->lastLoginLocation->name,
                    'code' => $admin->lastLoginLocation->code,
                ] : null,
                'accessible_locations' => $accessibleLocations->map(fn($loc) => [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'code' => $loc->code,
                    'is_active' => $loc->is_active,
                ]),
            ],
        ]);
    }

    public function switchLocation(Request $request): JsonResponse
    {
        $request->validate([
            'location_id' => ['required', 'integer', 'exists:locations,id'],
        ]);

        $admin = $request->user();
        $this->switchLocationAction->execute($admin, $request->location_id);

        return response()->json([
            'success' => true,
            'message' => 'Location switched successfully',
        ]);
    }
}