<?php

namespace App\Http\Controllers\Student;

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
     * Display the student's profile settings and linked parent/guardian visibility.
     */
    public function edit(Request $request): View
    {
        /** @var User $student */
        $student = $request->user();
        $parents = $student->parents()->get();

        return view('student.profile.edit', compact('student', 'parents'));
    }

    /**
     * Update the student's profile information and credentials.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $student */
        $student = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($student->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $student->name = $validated['name'];
        $student->email = $validated['email'];
        $student->phone = $validated['phone'] ?? null;
        $student->bio = $validated['bio'] ?? null;

        if ($request->hasFile('avatar')) {
            if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
                Storage::disk('public')->delete($student->avatar);
            }
            $student->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if (! empty($validated['password'])) {
            $student->password = Hash::make($validated['password']);
        }

        $student->save();

        return redirect()->route('student.profile.edit')
            ->with('success', 'Your student profile and security credentials have been updated.');
    }
}
