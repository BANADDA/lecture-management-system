<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    /**
     * Display a listing of the programs.
     */
    public function index(Request $request)
{
    // Start a query builder instance
    $query = Program::with('department');

    // Filter by search term (name or code)
    if ($search = $request->input('search')) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('code', 'LIKE', "%{$search}%");
        });
    }

    // Filter by department if provided
    if ($departmentId = $request->input('department_id')) {
        $query->where('department_id', $departmentId);
    }

    // Sorting
    if ($sort = $request->input('sort')) {
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'code_asc':
                $query->orderBy('code', 'asc');
                break;
            case 'code_desc':
                $query->orderBy('code', 'desc');
                break;
        }
    } else {
        $query->orderBy('name', 'asc');
    }

    // Paginate the results (10 per page)
    $programs = $query->paginate(10);

    // Also fetch departments for the filter dropdown
    $departments = \App\Models\Department::all();

    return view('admin.programs.index', compact('programs', 'departments'));
}

    /**
     * Show the form for creating a new program.
     */
    public function create()
    {
        // Get all departments to populate a dropdown
        $departments = Department::all();
        return view('admin.programs.create', compact('departments'));
    }

    /**
     * Store a newly created program.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:50',
            'duration_years'=> 'required|integer|min:1',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $validatedData['image_url'] = $request->file('image')->store('programs', 'public');
            }
            Program::create($validatedData);
            DB::commit();
            return redirect()->route('admin.programs.index')
                ->with('success', 'Program created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Program creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create program.');
        }
    }

    /**
     * Display the specified program.
     */
    public function show(Program $program)
    {
        // Load the department relation for additional context.
        $program->load('department');
        return view('admin.programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit(Program $program)
    {
        $departments = Department::all();
        return view('admin.programs.edit', compact('program', 'departments'));
    }

    /**
     * Update the specified program.
     */
    public function update(Request $request, Program $program)
    {
        $validatedData = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:50',
            'duration_years'=> 'required|integer|min:1',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                // Delete the old image if exists.
                if ($program->image_url) {
                    Storage::disk('public')->delete($program->image_url);
                }
                $validatedData['image_url'] = $request->file('image')->store('programs', 'public');
            }

            $program->update($validatedData);
            DB::commit();
            return redirect()->route('admin.programs.index')
                ->with('success', 'Program updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Program update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update program.');
        }
    }

    /**
     * Remove the specified program.
     */
    public function destroy(Program $program)
    {
        DB::beginTransaction();
        try {
            if ($program->image_url) {
                Storage::disk('public')->delete($program->image_url);
            }
            $program->delete();
            DB::commit();
            return redirect()->route('admin.programs.index')
                ->with('success', 'Program deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Program deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete program.');
        }
    }
}
