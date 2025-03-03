<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacultyRequest; // Create this request
use App\Http\Requests\UpdateFacultyRequest; // Create this request
use App\Models\Faculty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FacultyController extends Controller
{
    /**
     * Display a listing of faculties.
     */
    public function index()
    {
        $this->authorize('viewAny', Faculty::class);

        $faculties = Faculty::withCount('departments', 'programs')->get();
        return view('admin.faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new faculty.
     */
    public function create()
    {
        $this->authorize('create', Faculty::class);
        return view('admin.faculties.create');
    }

    /**
     * Store a newly created faculty.
     */
    public function store(StoreFacultyRequest $request)
    {
        $this->authorize('create', Faculty::class);

        DB::beginTransaction();
        try {
            // Handle file upload
            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('faculties', 'public')
                : null;

            $faculty = Faculty::create([
                'name' => $request->validated()['name'],
                'code' => $request->validated()['code'],
                'description' => $request->validated()['description'] ?? null,
                'image_url' => $imagePath
            ]);

            DB::commit();

            return redirect()->route('admin.faculties.index')
                ->with('success', 'Faculty created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Optional: Log the error
            \Log::error('Faculty creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create faculty. Please try again.');
        }
    }

    /**
     * Display specific faculty details.
     */
    public function show(Faculty $faculty)
    {
        $this->authorize('view', $faculty);

        $faculty->load('departments');
        $departmentsCount = $faculty->departments->count();
        $programsCount = $faculty->programs->count();
        $studentsCount = $faculty->students_count;

        return view('admin.faculties.show', compact('faculty', 'departmentsCount', 'programsCount', 'studentsCount'));
    }

    /**
     * Show edit form for a faculty.
     */
    public function edit(Faculty $faculty)
    {
        $this->authorize('update', $faculty);
        return view('admin.faculties.edit', compact('faculty'));
    }

    /**
     * Update a faculty.
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        $this->authorize('update', $faculty);

        DB::beginTransaction();
        try {
            $validatedData = $request->validated();

            // Handle file upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($faculty->image_url) {
                    Storage::disk('public')->delete($faculty->image_url);
                }
                $validatedData['image_url'] = $request->file('image')->store('faculties', 'public');
            }

            $faculty->update($validatedData);

            DB::commit();

            return redirect()->route('admin.faculties.index')
                ->with('success', 'Faculty updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Faculty update failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update faculty. Please try again.');
        }
    }

    /**
     * Delete a faculty.
     */
    public function destroy(Faculty $faculty)
{
    DB::beginTransaction();
    try {
        if ($faculty->departments->count() > 0) {
            DB::rollBack();
            return redirect()->route('admin.faculties.index')
                ->with('error', 'Cannot delete faculty with existing departments.');
        }

        if ($faculty->image_url) {
            Storage::disk('public')->delete($faculty->image_url);
        }

        $faculty->delete();
        DB::commit();

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty deleted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Faculty deletion failed: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to delete faculty. Please try again.');
    }
}

}
