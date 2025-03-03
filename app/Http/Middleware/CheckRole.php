<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param string $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Log detailed debugging information
        Log::info('CheckRole Middleware', [
            'requested_role' => $role,
            'is_authenticated' => Auth::check(),
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('User not authenticated');
            return redirect('login');
        }

        // Get the authenticated user
        $user = Auth::user();

        // Log user details
        Log::info('User Details', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_email' => $user->email,
        ]);

        // Check if the user has the required role
        if ($user->role !== $role) {
            Log::warning('Role Mismatch', [
                'expected_role' => $role,
                'actual_role' => $user->role,
            ]);

            // Redirect based on user's actual role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'You do not have permission to access that page.');
            } elseif ($user->isLecturer()) {
                return redirect()->route('lecturer.dashboard')
                    ->with('error', 'You do not have permission to access that page.');
            } elseif ($user->isStudent()) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'You do not have permission to access that page.');
            }

            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access that page.');
        }

        return $next($request);
    }
}
