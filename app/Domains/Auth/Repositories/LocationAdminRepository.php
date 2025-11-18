<?php

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Models\Location;

class LocationAdminRepository
{
    public function create(array $data): Location
    {
        return Location::create($data);
    }

    public function findByEmail(string $email): ?Location
    {
        return Location::where('email', $email)->first();
    }

    public function updateLastLogin(Location $admin, string $ip): void
    {
        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }
}
