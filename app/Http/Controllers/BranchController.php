<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::withCount(['users', 'complaints'])
            ->orderBy('name')
            ->paginate(20);

        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches,name'],
            'code' => ['nullable', 'string', 'max:20', 'unique:branches,code'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // Sanitize inputs
            $validated['name'] = strip_tags($validated['name']);
            if (!empty($validated['code'])) {
                $validated['code'] = strtoupper(strip_tags($validated['code']));
            }
            if (!empty($validated['location'])) {
                $validated['location'] = strip_tags($validated['location']);
            }

            $branch = Branch::create($validated);

            // Log activity
            ActivityLogService::log(
                'branch_created',
                "Created branch: {$branch->name}",
                $branch
            );

            return redirect()->route('branches.index')
                ->with('success', '✅ Branch "' . $branch->name . '" created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating branch: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Failed to create branch. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        $branch->load(['users', 'complaints']);
        return view('branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:branches,name,' . $branch->id],
                'code' => ['nullable', 'string', 'max:20', 'unique:branches,code,' . $branch->id],
                'location' => ['nullable', 'string', 'max:255'],
            ]);

            // Sanitize inputs
            $validated['name'] = strip_tags($validated['name']);
            if (!empty($validated['code'])) {
                $validated['code'] = strtoupper(strip_tags($validated['code']));
            }
            if (!empty($validated['location'])) {
                $validated['location'] = strip_tags($validated['location']);
            }

            $changes = array_diff_assoc($validated, $branch->only(array_keys($validated)));
            $branch->update($validated);

            // Log activity
            ActivityLogService::log(
                'branch_updated',
                "Updated branch: {$branch->name}",
                $branch,
                $changes
            );

            return redirect()->route('branches.index')
                ->with('success', '✅ Branch "' . $branch->name . '" updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', '❌ Please check the form for errors.');
        } catch (\Exception $e) {
            Log::error('Error updating branch: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Failed to update branch. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        try {
            $branchName = $branch->name;

            // Check if branch has users or complaints
            if ($branch->users()->count() > 0) {
                return back()->with('error', '⚠️ Cannot delete branch "' . $branchName . '" because it has assigned users.');
            }

            if ($branch->complaints()->count() > 0) {
                return back()->with('error', '⚠️ Cannot delete branch "' . $branchName . '" because it has associated tickets.');
            }

            // Log activity before deletion
            ActivityLogService::log(
                'branch_deleted',
                "Deleted branch: {$branchName}",
                $branch
            );

            $branch->delete();

            return redirect()->route('branches.index')
                ->with('success', '✅ Branch "' . $branchName . '" deactivated successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting branch: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', '❌ Failed to delete branch. Please try again.');
        }
    }
}
