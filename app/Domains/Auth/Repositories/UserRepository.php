<?php

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByEmail(string $email, ?int $locationId = null): ?User
    {
        $query = User::where('email', $email);
        
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        
        return $query->first();
    }

    public function updateLastLogin(User $user, string $ip): void
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function findByLocation(int $locationId)
    {
        return User::with('role')
            ->where('location_id', $locationId)
            ->where('status', 'active')
            ->get();
    }
    public function validateCredentials(string $username, string $password, string $franchiseId, string $businessUnitId): ?User
    {
        $user = User::with(['roles', 'franchise', 'businessUnit'])
            ->where('username', $username)
            ->where('franchise_id', $franchiseId)
            ->where('business_unit_id', $businessUnitId)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }
}
