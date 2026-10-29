<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and role filters.
     */
    public function index(Request $request): View
    {
        $roleFilter = $request->query('role');
        $search = $request->query('search');

        $query = User::with(['roles', 'children', 'parents'])->latest();

        if ($roleFilter) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $roleFilter));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles', 'roleFilter', 'search'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = Role::all();
        $students = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->get();

        return view('admin.users.create', compact('roles', 'students'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'children' => ['nullable', 'array'],
            'children.*' => ['exists:users,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->sync($validated['roles']);

        // If user has 'parent' role and children were selected
        if (! empty($validated['children'])) {
            $user->children()->sync($validated['children']);
        }

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} created successfully.");
    }

    /**
     * Display the specified user details and linked records.
     */
    public function show(User $user): View
    {
        $user->load([
            'roles.permissions',
            'children',
            'parents',
            'taughtClasses.course',
            'enrolledClasses.course',
            'invoices.items',
            'recordedPayments.invoice',
        ]);

        $availableStudents = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $user->children->pluck('id'))
            ->get();

        return view('admin.users.show', compact('user', 'availableStudents'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        $students = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))
            ->where('id', '!=', $user->id)
            ->get();

        return view('admin.users.edit', compact('user', 'roles', 'students'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'children' => ['nullable', 'array'],
            'children.*' => ['exists:users,id'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->roles()->sync($validated['roles']);

        if ($user->hasRole('parent')) {
            $user->children()->sync($validated['children'] ?? []);
        }

        return redirect()->route('admin.users.show', $user)->with('success', "User {$user->name} updated successfully.");
    }

    /**
     * Attach a child student to a parent.
     */
    public function linkChild(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
        ]);

        $user->children()->syncWithoutDetaching([$validated['student_id']]);

        return back()->with('success', 'Student linked to parent successfully.');
    }

    /**
     * Detach a child student from a parent.
     */
    public function unlinkChild(User $user, User $child): RedirectResponse
    {
        $user->children()->detach($child->id);

        return back()->with('success', 'Student unlinked from parent.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User {$userName} removed successfully.");
    }
}
