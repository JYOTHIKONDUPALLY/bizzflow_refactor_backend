<?php

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Models\User;
use App\Domains\Auth\DataTransferObjects\RegisterUserData;
use App\Domains\Auth\Repositories\UserRepository;
use App\Domains\Auth\Events\UserRegistered;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public function __construct(
        private UserRepository $userRepo
    ) {}

    public function execute(RegisterUserData $data): User
    {
        $user = $this->userRepo->create([
            'franchise_id' => $data->franchise_id,
            'location_id' => $data->location_id,
            'role_id' => $data->role_id,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'phone' => $data->phone,
            'password' => Hash::make($data->password),
            'status' => 'active',
        ]);

        event(new UserRegistered($user));

        return $user;
    }
}
