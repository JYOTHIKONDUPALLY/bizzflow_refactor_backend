<?php

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Models\FranchiseAdmin;

class FranchiseAdminRepository
{
    public function create(array $data): FranchiseAdmin
    {
        return FranchiseAdmin::create($data);
    }

    public function findByEmail(string $email): ?FranchiseAdmin
    {
        return FranchiseAdmin::where('email', $email)->first();
    }

    public function updateLastLogin(FranchiseAdmin $admin, string $ip, ?int $locationId = null): void
    {
        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'last_login_location_id' => $locationId,
        ]);
    }
}
