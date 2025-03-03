<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LecturerController extends Controller
{
    /**
     * Display a listing of the lecturers.
     */
    public function index(Request $request)
    {
        $query = Lecturer::with('department');

        // Filter by search term on staff_id or lecturer name.
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('staff_id', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by department if provided.
        if ($dept = $request->input('department_id')) {
            $query->where('department_id', $dept);
        }

        // Order by staff_id ascending.
        $query->orderBy('staff_id', 'asc');

        $lecturers = $query->paginate(10);
        $departments = Department::all();

        return view('admin.lecturers.index', compact('lecturers', 'departments'));
    }

    /**
     * Show the form for creating a new lecturer.
     */
    public function create()
    {
        $departments = Department::all();
        $users = User::where('role', 'lecturer')->get();
        return view('admin.lecturers.create', compact('departments', 'users'));
    }

    /**
     * Store a newly created lecturer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'         => 'required|exists:users,id',
            'staff_id'        => 'required|string|max:50|unique:lecturers,staff_id',
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'department_id'   => 'required|exists:departments,id',
            'office_location' => 'nullable|string|max:255',
            'office_hours'    => 'nullable|array',
            'profile_photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status'          => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('profile_photo')) {
                $validated['profile_photo'] = $request->file('profile_photo')->store('lecturers', 'public');
            }
            Lecturer::create($validated);
            DB::commit();

            return redirect()->route('admin.lecturers.index')
                ->with('success', 'Lecturer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Lecturer creation failed: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create lecturer.');
        }
    }

    /**
     * Display the specified lecturer.
     */
    public function show(Lecturer $lecturer)
    {
        $lecturer->load('department');
        return view('admin.lecturers.show', compact('lecturer'));
    }

    /**
     * Show the form for editing the specified lecturer.
     */
    public function edit(Lecturer $lecturer)
    {
        $departments = Department::all();
        $users = User::where('role', 'lecturer')->get();
        return view('admin.lecturers.edit', compact('lecturer', 'departments', 'users'));
    }

    /**
     * Update the specified lecturer.
     */
    public function update(Request $request, Lecturer $lecturer)
    {
        $validated = $request->validate([
            'user_id'         => 'required|exists:users,id',
            'staff_id'        => 'required|string|max:50|unique:lecturers,staff_id,' . $lecturer->id,
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'department_id'   => 'required|exists:departments,id',
            'office_location' => 'nullable|string|max:255',
            'office_hours'    => 'nullable|array',
            'profile_photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status'          => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('profile_photo')) {
                if ($lecturer->profile_photo) {
                    Storage::disk('public')->delete($lecturer->profile_photo);
                }
                $validated['profile_photo'] = $request->file('profile_photo')->store('lecturers', 'public');
            }
            $lecturer->update($validated);
            DB::commit();

            return redirect()->route('admin.lecturers.index')
                ->with('success', 'Lecturer updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Lecturer update failed: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update lecturer.');
        }
    }

    /**
     * Remove the specified lecturer.
     */
    public function destroy(Lecturer $lecturer)
    {
        DB::beginTransaction();
        try {
            if ($lecturer->profile_photo) {
                Storage::disk('public')->delete($lecturer->profile_photo);
            }
            $lecturer->delete();
            DB::commit();

            return redirect()->route('admin.lecturers.index')
                ->with('success', 'Lecturer deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Lecturer deletion failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete lecturer.');
        }
    }
}
