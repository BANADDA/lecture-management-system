<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index()
    {
        // Eager load faculty for each department
        $departments = Department::with('faculty')->get();
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        // Retrieve all faculties to populate a select box
        $faculties = Faculty::all();
        return view('admin.departments.create', compact('faculties'));
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'faculty_id'  => 'required|exists:faculties,id',
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50',
            'description' => 'nullable|string',
            'campus'      => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $validatedData['image_url'] = $request->file('image')->store('departments', 'public');
            }
            Department::create($validatedData);
            DB::commit();

            return redirect()->route('admin.departments.index')
                ->with('success', 'Department created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Department creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create department.');
        }
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        $faculties = Faculty::all();
        return view('admin.departments.edit', compact('department', 'faculties'));
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, Department $department)
    {
        $validatedData = $request->validate([
            'faculty_id'  => 'required|exists:faculties,id',
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50',
            'description' => 'nullable|string',
            'campus'      => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                // Remove the old image if it exists
                if ($department->image_url) {
                    Storage::disk('public')->delete($department->image_url);
                }
                $validatedData['image_url'] = $request->file('image')->store('departments', 'public');
            }

            $department->update($validatedData);
            DB::commit();

            return redirect()->route('admin.departments.index')
                ->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Department update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update department.');
        }
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department)
    {
        DB::beginTransaction();
        try {
            if ($department->image_url) {
                Storage::disk('public')->delete($department->image_url);
            }
            $department->delete();
            DB::commit();

            return redirect()->route('admin.departments.index')
                ->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Department deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete department.');
        }
    }
}
