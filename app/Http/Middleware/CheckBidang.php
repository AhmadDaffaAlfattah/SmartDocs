<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBidang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $bidang): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Check if user's bidang matches the required bidang
        // Normalized comparison (lowercase)
        if (strtolower($user->bidang) === strtolower($bidang)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access to this department.');
    }
}
