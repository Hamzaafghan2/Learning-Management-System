<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfLoggedIn
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // If the user is logged in
        if (Auth::check() && Auth::user()->role === $role) {
            // Redirect to their dashboard
            switch ($role) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'instructor':
                    return redirect('/instructor/dashboard');
                case 'user':
                    return redirect('/dashboard');
            }
        }

        // Otherwise, continue to login page
        return $next($request);
    }
}
