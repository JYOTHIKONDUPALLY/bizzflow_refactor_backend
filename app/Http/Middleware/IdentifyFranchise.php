<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IdentifyFranchise
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && method_exists($user, 'franchise')) {
            // Set franchise context in request
            $request->merge([
                'current_franchise_id' => $user->franchise_id,
                'current_location_id' => $user->location_id ?? null,
            ]);

            // You can also set it globally
            config(['app.current_franchise_id' => $user->franchise_id]);
            config(['app.current_location_id' => $user->location_id ?? null]);
        }

        return $next($request);
    }
}