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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        // Models store password hashes in `password_hash` column
        if (!$user || !Hash::check($data->password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user is active (uncomment if needed)
        // if ($user->status !== 'active') {
        //     throw ValidationException::withMessages([
        //         'email' => ['Your account is not active. Please contact support.'],
        //     ]);
        // }

        // Set the provider for token creation
        $provider = $this->getProviderName($userType);
        config(['auth.guards.api.provider' => $provider]);

        // Revoke all previous tokens for this user
        $user->tokens()->delete();

        // Create Passport token with wildcard scope (or specific scopes if registered)
        $tokenResult = $user->createToken(
            'auth_token',
            ['*'] // Wildcard grants all permissions
        );

        $accessToken = $tokenResult->accessToken;
        $token = $tokenResult->token;

        // Set token expiration (optional, defaults to config)
        // $token->expires_at = now()->addDays(30);
        // $token->save();

        // Log the login event
        $this->logAuthEvent($user, $userType, 'login', $ip, $token->id);

        // Update last login
        $this->updateLastLogin($user, $userType, $ip, $data->location_id);

        // Prepare response with Passport token
        return $this->prepareAuthResponse($user, $accessToken, $userType, $token);
    }

    public function logout($user): void
    {
        $userType = $this->getUserType($user);
        
        // Log the logout event with current token
        $currentToken = $user->token();
        $this->logAuthEvent($user, $userType, 'logout', request()->ip(), $currentToken?->id);
        
        // Revoke current access token
        if ($currentToken) {
            $currentToken->revoke();
        }
        
        // Optional: Revoke all tokens for this user
        // $user->tokens()->delete();
    }

    public function refreshToken($user): AuthResponseData
    {
        $userType = $this->getUserType($user);
        
        // Revoke current token
        $user->token()->revoke();
        
        // Create new token with wildcard scope
        $tokenResult = $user->createToken(
            'auth_token',
            ['*']
        );

        $accessToken = $tokenResult->accessToken;
        $token = $tokenResult->token;

        // Log token refresh
        $this->logAuthEvent($user, $userType, 'token_refresh', request()->ip(), $token->id);

        return $this->prepareAuthResponse($user, $accessToken, $userType, $token);
    }

    private function findUserByType(string $email, UserType $userType, ?string $locationId = null)
    {
        return match($userType) {
            UserType::CUSTOMER => $this->customerRepo->findByEmail($email, $locationId),
            UserType::USER => $this->userRepo->findByEmail($email, $locationId),
            UserType::LOCATION_ADMIN => $this->locationRepo->findByEmail($email),
            UserType::FRANCHISE_ADMIN => $this->franchiseAdminRepo->findByEmail($email),
        };
    }

    private function updateLastLogin($user, UserType $userType, string $ip, ?string $locationId = null): void
    {
        match($userType) {
            UserType::CUSTOMER => $this->customerRepo->updateLastLogin($user, $ip),
            UserType::USER => $this->userRepo->updateLastLogin($user, $ip),
            UserType::LOCATION_ADMIN => $this->locationRepo->updateLastLogin($user, $ip),
            UserType::FRANCHISE_ADMIN => $this->franchiseAdminRepo->updateLastLogin($user, $ip, $locationId),
        };
    }

    private function getTokenScopes(UserType $userType): array
    {
        // Define scopes based on user type
        return match($userType) {
            UserType::CUSTOMER => ['customer'],
            UserType::USER => ['user'],
            UserType::LOCATION_ADMIN => ['location-admin'],
            UserType::FRANCHISE_ADMIN => ['franchise-admin'],
        };
    }

    private function getProviderName(UserType $userType): string
    {
        return match($userType) {
            UserType::CUSTOMER => 'customers',
            UserType::USER => 'users',
            UserType::LOCATION_ADMIN => 'location_admins',
            UserType::FRANCHISE_ADMIN => 'franchise_admins',
        };
    }

    private function prepareAuthResponse($user, string $accessToken, UserType $userType, $token = null): AuthResponseData
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
            token: $accessToken,
            type: 'Bearer',
            user: $userData,
            franchise: $franchise,
            location: $location,
            accessible_locations: $accessibleLocations,
        );
    }

    private function logAuthEvent($user, UserType $userType, string $eventType, ?string $ip, ?string $tokenId): void
    {
        DB::table('auth_logs')->insert([
            'id' => (string) Str::uuid(),
            'entity_id' => $user->id,
            'franchise_id' => $user->franchise_id ?? null,
            'business_unit_id' => $user->business_unit_id ?? null,
            'ip_address' => $ip,
            'user_agent' => request()->userAgent() ?? null,
            'event_type' => $eventType,
            'event_time' => now(),
            'apitoken' => $tokenId, // Store Passport token ID
            'entity_type' => $userType->value
        ]);
    }

    private function getUserType($user): UserType
    {
        return match(true) {
            $user instanceof Customer => UserType::CUSTOMER,
            $user instanceof User => UserType::USER,
            $user instanceof LocationAdmin => UserType::LOCATION_ADMIN,
            $user instanceof FranchiseAdmin => UserType::FRANCHISE_ADMIN,
            default => throw new \RuntimeException('Unknown user type')
        };
    }
}