<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index(Request $request)
    {
        // Start query builder with related program and department if needed.
        $query = Course::with('program.department');

        // Optional filtering (by name, code, etc.) can be added here.
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        // Sorting (example: by name ascending)
        $query->orderBy('name', 'asc');

        // Paginate, 10 per page
        $courses = $query->paginate(10);

        // Pass list of programs if you want a filter dropdown (optional)
        $programs = Program::all();

        return view('admin.courses.index', compact('courses', 'programs'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        // Retrieve all programs for the dropdown.
        $programs = Program::all();
        return view('admin.courses.create', compact('programs'));
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'program_id'  => 'required|exists:programs,id',
            'code'        => 'required|string|max:50',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'year'        => 'required|integer|min:1',
            'semester'    => 'required|integer|in:1,2',
            'credits'     => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $validatedData['image_url'] = $request->file('image')->store('courses', 'public');
            }

            Course::create($validatedData);
            DB::commit();

            return redirect()->route('admin.courses.index')
                ->with('success', 'Course created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Course creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create course.');
        }
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load('program.department');
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        $programs = Program::all();
        return view('admin.courses.edit', compact('course', 'programs'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validatedData = $request->validate([
            'program_id'  => 'required|exists:programs,id',
            'code'        => 'required|string|max:50',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'year'        => 'required|integer|min:1',
            'semester'    => 'required|integer|in:1,2',
            'credits'     => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                if ($course->image_url) {
                    Storage::disk('public')->delete($course->image_url);
                }
                $validatedData['image_url'] = $request->file('image')->store('courses', 'public');
            }

            $course->update($validatedData);
            DB::commit();

            return redirect()->route('admin.courses.index')
                ->with('success', 'Course updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Course update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update course.');
        }
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        DB::beginTransaction();
        try {
            if ($course->image_url) {
                Storage::disk('public')->delete($course->image_url);
            }
            $course->delete();
            DB::commit();

            return redirect()->route('admin.courses.index')
                ->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Course deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete course.');
        }
    }
}
