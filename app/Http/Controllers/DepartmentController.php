<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::withCount(['complaints'])
            ->orderBy('name')
            ->paginate(20);

        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        try {
            // Sanitize inputs
            $validated['name'] = strip_tags($validated['name']);
            if (!empty($validated['description'])) {
                $validated['description'] = strip_tags($validated['description']);
            }
            $validated['is_active'] = $request->has('is_active');

            $department = Department::create($validated);

            // Log activity
            ActivityLogService::log(
                'department_created',
                "Created department: {$department->name}",
                $department
            );

            return redirect()->route('departments.index')
                ->with('success', '✅ Department "' . $department->name . '" created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating department: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Failed to create department. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
                'description' => ['nullable', 'string', 'max:1000'],
                'is_active' => ['boolean'],
            ]);

            // Sanitize inputs
            $validated['name'] = strip_tags($validated['name']);
            if (!empty($validated['description'])) {
                $validated['description'] = strip_tags($validated['description']);
            }
            $validated['is_active'] = $request->has('is_active');

            $department->update($validated);

            // Log activity
            ActivityLogService::log(
                'department_updated',
                "Updated department: {$department->name}",
                $department
            );

            return redirect()->route('departments.index')
                ->with('success', '✅ Department "' . $department->name . '" updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating department: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Failed to update department. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        try {
            // Check if department has associated complaints
            if ($department->complaints()->count() > 0) {
                return redirect()->back()
                    ->with('error', '❌ Cannot delete department "'.$department->name.'" because it has associated tickets.');
            }

            $name = $department->name;
            $department->delete();

            // Log activity
            ActivityLogService::log(
                'department_deleted',
                "Deleted department: {$name}",
                null
            );

            return redirect()->route('departments.index')
                ->with('success', '✅ Department "'.$name.'" deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting department: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', '❌ Failed to delete department. Please try again.');
        }
    }
}
