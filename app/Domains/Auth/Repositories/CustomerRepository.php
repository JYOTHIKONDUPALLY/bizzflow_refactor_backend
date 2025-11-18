<?php

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Models\Customer;

class CustomerRepository
{
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function findByEmail(string $email, ?int $businessUnitId = null): ?Customer
    {
        $query = Customer::where('email', $email);
        
        if ($businessUnitId) {
            $query->where('business_unit_id', $businessUnitId);
        }
        
        return $query->first();
    }

    public function updateLastLogin(Customer $customer, string $ip): void
    {
        $customer->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function findByFranchiseAndLocation(int $franchiseId, int $businessUnitId)
    {
        return Customer::where('franchise_id', $franchiseId)
            ->where('business_unit_id', $businessUnitId)
            ->get();
    }
}
