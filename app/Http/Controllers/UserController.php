<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = ['super_admin', 'manager', 'support_agent', 'user'];
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:super_admin,manager,support_agent,user'],
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);
            $validated['email_verified_at'] = now();

            $user = User::create($validated);

            // Log activity
            ActivityLogService::logUserCreated($user);

            return redirect()->route('users.index')
                ->with('success', '✅ User "' . $user->name . '" created successfully with role: ' . ucwords(str_replace('_', ' ', $user->role)));
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Failed to create user. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = ['super_admin', 'manager', 'support_agent', 'user'];
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'role' => ['required', 'in:super_admin,manager,support_agent,user'],
            ]);

            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['required', 'string', 'min:8', 'confirmed'],
                ]);
                $validated['password'] = Hash::make($request->password);
            }

            $changes = array_diff_assoc($validated, $user->only(array_keys($validated)));
            $user->update($validated);

            // Log activity
            ActivityLogService::logUserUpdated($user, $changes);

            return redirect()->route('users.index')
                ->with('success', '✅ User "' . $user->name . '" updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', '❌ Please check the form for errors.');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Failed to update user. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', '⚠️ You cannot delete your own account.');
        }

        try {
            $userName = $user->name;

            // Log activity before deletion
            ActivityLogService::logUserDeleted($user);

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', '✅ User "' . $userName . '" deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', '❌ Failed to delete user. This user may have assigned tickets.');
        }
    }
}
