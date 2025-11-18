<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domains\Auth\Models\Customer;
use App\Domains\Auth\Models\User;
use App\Models\User as StaffUser;
use App\Models\LocationAdmin;
use App\Models\FranchiseAdmin;

class ValidateCustomToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the token from the Authorization header
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $plainToken = substr($authHeader, 7); // Remove "Bearer " prefix
        $tokenHash = hash('sha256', $plainToken);

        // Look up the token in auth_logs
        $authLog = DB::table('auth_logs')
            ->where('apitoken', $tokenHash)
            ->where('event_type', 'login') // Only login events are valid tokens
            ->latest('event_time')
            ->first();

        if (!$authLog) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Load the user based on user_id
        $user = $this->loadUserByType($authLog->user_id);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Set the authenticated user on the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    /**
     * Load user by determining their type from auth_logs or by querying all possible models.
     *
     * @param  string  $userId
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function loadUserByType(string $userId): ?Model
    {
        // Try Customer first
        $user = Customer::find($userId);
        if ($user) {
            return $user;
        }

        // Try User (staff)
        $user = User::find($userId);
        if ($user) {
            return $user;
        }

        // Add other user models if they exist
        // $user = LocationAdmin::find($userId);
        // if ($user) {
        //     return $user;
        // }

        return null;
    }
}
