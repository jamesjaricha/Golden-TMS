<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Services\ActivityLogService;
use App\Services\LookupDataService;
use Illuminate\Http\Request;

class EmployerController extends Controller
{
    public function index()
    {
        $employers = Employer::withCount('complaints')->latest()->paginate(20);
        return view('employers.index', compact('employers'));
    }

    public function create()
    {
        return view('employers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:employers,name',
            'is_active' => 'boolean',
        ]);

        // Sanitise input to prevent XSS
        $validated['name'] = strip_tags($validated['name']);

        $employer = Employer::create($validated);

        // Clear cache as data has changed
        LookupDataService::clearEmployerCache();

        ActivityLogService::log(
            'employer_created',
            "Created employer: {$employer->name}",
            $employer
        );

        return redirect()->route('employers.index')->with('success', 'Employer created successfully.');
    }

    public function edit(Employer $employer)
    {
        $employer->loadCount('complaints');
        return view('employers.edit', compact('employer'));
    }

    public function update(Request $request, Employer $employer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:employers,name,' . $employer->id,
            'is_active' => 'boolean',
        ]);

        // Sanitise input to prevent XSS
        $validated['name'] = strip_tags($validated['name']);

        $oldName = $employer->name;
        $employer->update($validated);

        // Clear cache as data has changed
        LookupDataService::clearEmployerCache();

        ActivityLogService::log(
            'employer_updated',
            "Updated employer: {$oldName} to {$employer->name}",
            $employer
        );

        return redirect()->route('employers.index')->with('success', 'Employer updated successfully.');
    }

    public function destroy(Employer $employer)
    {
        // Prevent deletion if employer has associated tickets
        if ($employer->complaints()->count() > 0) {
            return back()->with('error', 'Cannot delete employer with associated tickets.');
        }

        $name = $employer->name;
        $employer->delete();

        // Clear cache as data has changed
        LookupDataService::clearEmployerCache();

        ActivityLogService::log(
            'employer_deactivated',
            "Deactivated employer: {$name}"
        );

        return redirect()->route('employers.index')->with('success', 'Employer deactivated successfully.');
    }
}
