<?php

// app/Http/Middleware/RedirectIfAuthenticated.php
namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect based on user role
                $user = Auth::guard($guard)->user();

                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->isLecturer()) {
                    return redirect()->route('lecturer.dashboard');
                } elseif ($user->isStudent()) {
                    return redirect()->route('student.dashboard');
                }

                // Default redirect if no specific role
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
