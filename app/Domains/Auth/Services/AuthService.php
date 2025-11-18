<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\Customer;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Models\LocationAdmin;
use App\Domains\Auth\Models\FranchiseAdmin;
use App\Domains\Auth\DataTransferObjects\LoginData;
use App\Domains\Auth\DataTransferObjects\AuthResponseData;
use App\Domains\Auth\Enums\UserType;
use App\Domains\Auth\Repositories\CustomerRepository;
use App\Domains\Auth\Repositories\UserRepository;
use App\Domains\Auth\Repositories\LocationAdminRepository;
use App\Domains\Auth\Repositories\FranchiseAdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private CustomerRepository $customerRepo,
        private UserRepository $userRepo,
        private LocationAdminRepository $locationRepo,
        private FranchiseAdminRepository $franchiseAdminRepo,
    ) {}


    public function login(LoginData $data, UserType $userType, string $ip): AuthResponseData
    {
        $user = $this->findUserByType($data->email, $userType, $data->location_id);

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Your account is not active. Please contact support.'],
            ]);
        }

        // Update last login
        $this->updateLastLogin($user, $userType, $ip, $data->location_id);

        // Create token
        $token = $user->createToken($userType->value . '_token')->plainTextToken;

        // Prepare response
        return $this->prepareAuthResponse($user, $token, $userType);
    }

    private function findUserByType(string $email, UserType $userType, ?int $locationId = null)
    {
        return match($userType) {
            UserType::CUSTOMER => $this->customerRepo->findByEmail($email, $locationId),
            UserType::USER => $this->userRepo->findByEmail($email, $locationId),
            UserType::LOCATION_ADMIN => $this->locationAdminRepo->findByEmail($email),
            UserType::FRANCHISE_ADMIN => $this->franchiseAdminRepo->findByEmail($email),
        };
    }

    private function updateLastLogin($user, UserType $userType, string $ip, ?int $locationId = null): void
    {
        match($userType) {
            UserType::CUSTOMER => $this->customerRepo->updateLastLogin($user, $ip),
            UserType::USER => $this->userRepo->updateLastLogin($user, $ip),
            UserType::LOCATION_ADMIN => $this->locationAdminRepo->updateLastLogin($user, $ip),
            UserType::FRANCHISE_ADMIN => $this->franchiseAdminRepo->updateLastLogin($user, $ip, $locationId),
        };
    }

    private function prepareAuthResponse($user, string $token, UserType $userType): AuthResponseData
    {
        $userData = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'status' => $user->status,
            'type' => $userType->value,
        ];

        $franchise = null;
        $location = null;
        $accessibleLocations = null;

        if (method_exists($user, 'franchise') && $user->franchise) {
            $franchise = [
                'id' => $user->franchise->id,
                'name' => $user->franchise->name,
                'slug' => $user->franchise->slug,
            ];
        }

        if (method_exists($user, 'location') && $user->location) {
            $location = [
                'id' => $user->location->id,
                'name' => $user->location->name,
                'code' => $user->location->code,
            ];
        }

        // For Franchise Admin, get all accessible locations
        if ($userType === UserType::FRANCHISE_ADMIN) {
            $accessibleLocations = $user->getAccessibleLocations()->map(function ($loc) {
                return [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'code' => $loc->code,
                    'is_active' => $loc->is_active,
                ];
            })->toArray();
        }

        // Add role for users
        if ($userType === UserType::USER && $user->role) {
            $userData['role'] = [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'slug' => $user->role->slug,
                'permissions' => $user->role->permissions ?? [],
            ];
        }

        return new AuthResponseData(
            token: $token,
            type: 'Bearer',
            user: $userData,
            franchise: $franchise,
            location: $location,
            accessible_locations: $accessibleLocations,
        );
    }

    public function logout($user): void
    {
        $user->currentAccessToken()->delete();
    }
}
