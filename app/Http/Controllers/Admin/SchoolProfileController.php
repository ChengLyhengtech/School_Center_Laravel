<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolProfileController extends Controller
{
    /**
     * Show the form for editing the school profile.
     */
    public function edit(): View
    {
        $profile = SchoolProfile::first() ?? SchoolProfile::create([
            'name' => 'CLH School Center',
            'tagline' => 'Excellence in Technology and Language Education',
            'email' => 'info@clh-school.edu.kh',
            'phone' => '+855 12 345 678',
            'address' => 'Phnom Penh, Cambodia',
            'social_links' => [
                'facebook' => 'https://facebook.com',
                'telegram' => 'https://t.me',
                'youtube' => 'https://youtube.com',
                'website' => 'https://clh-school.edu.kh',
            ],
        ]);

        return view('admin.settings.profile', compact('profile'));
    }

    /**
     * Update the school profile in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $profile = SchoolProfile::first() ?? new SchoolProfile;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
        ]);

        // Process logo upload
        if ($request->hasFile('logo')) {
            if ($profile->logo && ! str_starts_with($profile->logo, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $profile->logo));
            }
            $logoPath = $request->file('logo')->store('school', 'public');
            $profile->logo = Storage::url($logoPath);
        }

        // Process banner upload
        if ($request->hasFile('banner')) {
            if ($profile->banner && ! str_starts_with($profile->banner, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $profile->banner));
            }
            $bannerPath = $request->file('banner')->store('school', 'public');
            $profile->banner = Storage::url($bannerPath);
        }

        $profile->name = $validated['name'];
        $profile->tagline = $validated['tagline'] ?? null;
        $profile->address = $validated['address'] ?? null;
        $profile->phone = $validated['phone'] ?? null;
        $profile->email = $validated['email'] ?? null;
        $profile->social_links = [
            'facebook' => $validated['facebook'] ?? null,
            'telegram' => $validated['telegram'] ?? null,
            'youtube' => $validated['youtube'] ?? null,
            'website' => $validated['website'] ?? null,
        ];

        $profile->save();

        return redirect()->route('admin.settings.profile')->with('success', 'School Profile and Configuration updated successfully.');
    }
}
