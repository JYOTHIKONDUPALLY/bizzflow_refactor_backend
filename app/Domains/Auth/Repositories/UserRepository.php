<?php

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Models\User;
use App\Domains\Auth\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByEmail(string $email, ?string $businessUnitId = null): ?User
    {
        $query = User::where('email', $email);
        
        if ($businessUnitId) {
            $query->where('business_unit_id', $businessUnitId);
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
    public function getRoleIdByRoleName(string $roleName): int
    {
        return Role::where('name', $roleName)->first()->id;
    }
            public function insertUserRole($user_id, $role_id){
        DB::table('user_roles')->insert([
            'user_id' => $user_id,
            'role_id' => $role_id,
        ]);
    }
}
