<?php
namespace Presentation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Get token from request
        $tokenId = $request->bearerToken();
        
        // Get role from oauth_access_tokens
        $token = DB::table('oauth_access_tokens')
            ->where('id', $tokenId)
            ->where('user_id', $user->id)
            ->first();

        if (!$token || !$token->role) {
            return response()->json([
                'success' => false,
                'message' => 'No role assigned to token',
            ], 403);
        }

        // Check if user has any of the required roles
        if (!in_array($token->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions. Required roles: ' . implode(', ', $roles),
            ], 403);
        }

        return $next($request);
    }
}
