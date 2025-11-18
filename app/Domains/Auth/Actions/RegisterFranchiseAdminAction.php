<?php

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Models\FranchiseAdmin;
use App\Domains\Auth\DataTransferObjects\RegisterFranchiseAdminData;
use App\Domains\Auth\Repositories\FranchiseAdminRepository;
use Illuminate\Support\Facades\Hash;

class RegisterFranchiseAdminAction
{
    public function __construct(
        private FranchiseAdminRepository $franchiseAdminRepo
    ) {}

    public function execute(RegisterFranchiseAdminData $data): FranchiseAdmin
    {
        return $this->franchiseAdminRepo->create([
            'franchise_id' => $data->franchise_id,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'phone' => $data->phone,
            'password' => Hash::make($data->password),
            'status' => 'active',
        ]);
    }
}
