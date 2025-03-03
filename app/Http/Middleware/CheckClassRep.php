<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckClassRep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in and is a student
        if (!auth()->check() || !auth()->user()->isStudent()) {
            return redirect()->route('login');
        }

        // Check if student is a class representative
        $student = auth()->user()->student;
        if (!$student || !$student->isClassRepresentative()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Only class representatives have access to this area.');
        }

        return $next($request);
    }
}
