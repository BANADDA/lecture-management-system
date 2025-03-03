<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Course;

class CheckCourseAccess
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

        // Get the course ID from the route parameters
        $courseId = $request->route('course');

        // If it's an object, get its ID
        if ($courseId instanceof Course) {
            $courseId = $courseId->id;
        }

        // Check if the user has access to this course
        $user = auth()->user();
        if (!$user->canAccessCourse($courseId)) {
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access that course.');
            } elseif ($user->isLecturer()) {
                return redirect()->route('lecturer.dashboard')->with('error', 'You do not have permission to access that course.');
            } elseif ($user->isStudent()) {
                return redirect()->route('student.dashboard')->with('error', 'You do not have permission to access that course.');
            }
        }

        return $next($request);
    }
}
