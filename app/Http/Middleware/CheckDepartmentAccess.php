<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Department;

class CheckDepartmentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Get the department ID from the route parameters
        $departmentId = $request->route('department');

        // If it's an object, get its ID
        if ($departmentId instanceof Department) {
            $departmentId = $departmentId->id;
        }

        // Check if the user has access to this department
        $user = auth()->user();
        if (!$user->canAccessDepartment($departmentId)) {
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access that department.');
            } elseif ($user->isLecturer()) {
                return redirect()->route('lecturer.dashboard')->with('error', 'You do not have permission to access that department.');
            } elseif ($user->isStudent()) {
                return redirect()->route('student.dashboard')->with('error', 'You do not have permission to access that department.');
            }
        }

        return $next($request);
    }
}
