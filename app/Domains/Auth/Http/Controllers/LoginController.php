<?php

namespace App\Domains\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Domains\Auth\Services\AuthService;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    /**
     * Login user and generate token
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $response = $this->authService->login($validated);

        if (!$response['success']) {
            return response()->json([
                'message' => $response['message']
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token'   => $response['token'],
            'user'    => $response['user']
        ]);
    }


    /**
     * Refresh token (optional)
     */
    public function refreshToken(Request $request)
    {
        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed',
            'token' => $token
        ]);
    }


    /**
     * Logout user and revoke current token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
