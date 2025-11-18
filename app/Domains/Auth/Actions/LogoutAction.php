<?php

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Services\AuthService;

class LogoutAction
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function execute($user): void
    {
        $this->authService->logout($user);
    }
}
