<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckUserBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status === 'banned') {
            $allowedRoutes = ['banned', 'logout', 'language.change'];

            if (in_array($request->route()->getName(), $allowedRoutes)) {
                return $next($request);
            }

            return redirect()->route('banned');
        }

        return $next($request);
    }
}
