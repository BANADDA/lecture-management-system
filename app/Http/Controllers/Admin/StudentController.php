<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(Request $request)
    {
        $query = Student::with('program');

        // Filter by search term on student_id, first_name, or last_name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by program if provided
        if ($programId = $request->input('program_id')) {
            $query->where('program_id', $programId);
        }

        // Sorting - default by student_id ascending
        $query->orderBy('student_id', 'asc');

        // Paginate results (10 per page)
        $students = $query->paginate(10);

        // Get all programs for filter dropdown
        $programs = Program::all();

        return view('admin.students.index', compact('students', 'programs'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $programs = Program::all();
        // If you allow selecting from existing users
        $users = User::where('role', 'student')->get();
        return view('admin.students.create', compact('programs', 'users'));
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'         => 'required|exists:users,id',
            'student_id'      => 'required|string|max:50|unique:students,student_id',
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'program_id'      => 'required|exists:programs,id',
            'current_year'    => 'required|integer|min:1',
            'current_semester'=> 'required|integer|in:1,2',
            'profile_photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status'          => 'required|in:active,inactive'
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('profile_photo')) {
                $validated['profile_photo'] = $request->file('profile_photo')->store('students', 'public');
            }
            Student::create($validated);
            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Student creation failed: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create student.');
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load('program');
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $programs = Program::all();
        $users = User::where('role', 'student')->get();
        return view('admin.students.edit', compact('student', 'programs', 'users'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'user_id'         => 'required|exists:users,id',
            'student_id'      => 'required|string|max:50|unique:students,student_id,' . $student->id,
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'program_id'      => 'required|exists:programs,id',
            'current_year'    => 'required|integer|min:1',
            'current_semester'=> 'required|integer|in:1,2',
            'profile_photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status'          => 'required|in:active,inactive'
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('profile_photo')) {
                if ($student->profile_photo) {
                    Storage::disk('public')->delete($student->profile_photo);
                }
                $validated['profile_photo'] = $request->file('profile_photo')->store('students', 'public');
            }
            $student->update($validated);
            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Student update failed: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update student.');
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        DB::beginTransaction();
        try {
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            $student->delete();
            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Student deletion failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete student.');
        }
    }
}
