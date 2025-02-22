<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NonAdminMiddleware
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
        if (!Auth::check()) {
            // Redirect jika belum login
            return redirect()->route('admin.auth.login')->with('error', 'You must log in first.');
        }

        if (Auth::user()->is_admin) {
            // Redirect jika pengguna adalah admin
            return redirect()->route('admin.auth.login')->with('error', 'Admins cannot access this page.');
        }

        return $next($request);
    }
}
