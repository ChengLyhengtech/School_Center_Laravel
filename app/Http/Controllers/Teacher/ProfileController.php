<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the teacher's profile and credential settings.
     */
    public function edit(Request $request): View
    {
        $teacher = $request->user();

        return view('teacher.profile.edit', compact('teacher'));
    }

    /**
     * Update the teacher's profile, bio, qualifications, and credentials.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $teacher */
        $teacher = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($teacher->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'qualifications' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];
        $teacher->phone = $validated['phone'] ?? null;
        $teacher->bio = $validated['bio'] ?? null;
        $teacher->qualifications = $validated['qualifications'] ?? null;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if present
            if ($teacher->avatar && Storage::disk('public')->exists($teacher->avatar)) {
                Storage::disk('public')->delete($teacher->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $teacher->avatar = $path;
        }

        if (! empty($validated['password'])) {
            $teacher->password = Hash::make($validated['password']);
        }

        $teacher->save();

        return redirect()->route('teacher.profile.edit')
            ->with('success', 'Your teacher profile and credentials have been updated successfully.');
    }
}
