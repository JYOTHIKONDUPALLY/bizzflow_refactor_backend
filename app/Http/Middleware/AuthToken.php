<?php
namespace Presentation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Token;
use Symfony\Component\HttpFoundation\Response;

class AuthToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - No token provided',
            ], 401);
        }

        try {
            $token = Token::findOrFail($request->bearerToken());
            
            if ($token->revoked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token has been revoked',
                ], 401);
            }

            if ($token->expires_at && $token->expires_at->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token has expired',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }

        return $next($request);
    }
}