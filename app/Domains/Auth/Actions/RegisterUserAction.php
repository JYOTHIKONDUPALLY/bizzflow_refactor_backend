<?php

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Models\User;
use App\Domains\Auth\DataTransferObjects\RegisterUserData;
use App\Domains\Auth\Repositories\UserRepository;
use App\Domains\Auth\Events\UserRegistered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class RegisterUserAction
{
    public function __construct(
        private UserRepository $userRepo

    ) {}

    public function execute(RegisterUserData $data): User
    {
        $user = $this->userRepo->create([
            'franchise_id' => $data->franchise_id,
            'business_unit_id' => $data->business_unit_id,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'password_hash' => Hash::make($data->password),
            'is_active' => 1
        ]);

        $this->userRepo->insertUserRole($user->id, $data->role_id);


        // event(new UserRegistered($user));

        return $user;
    }

}
