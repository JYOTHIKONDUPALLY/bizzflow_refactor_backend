<?php

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\DataTransferObjects\LoginData;
use App\Domains\Auth\DataTransferObjects\AuthResponseData;
use App\Domains\Auth\Enums\UserType;
use App\Domains\Auth\Services\AuthService;
use App\Domains\Auth\Events\UserLoggedIn;

class LoginAction
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function execute(LoginData $data, UserType $userType, string $ip): AuthResponseData
    {
        $authResponse = $this->authService->login($data, $userType, $ip);

       return $authResponse;
    }
}
