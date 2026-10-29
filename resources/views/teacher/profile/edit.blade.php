<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                {{ __('Teacher Profile & Account Settings') }}
            </h2>
            <p class="text-sm text-slate-500 mt-1">Manage personal bio, qualifications, contact details, and account security.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <form method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Profile & Qualifications Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="font-extrabold text-slate-900 text-base">Public & Faculty Information</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Visible to administration, colleagues, and displayed on your academic roster.</p>
                    </div>

                    <!-- Avatar Upload -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Profile Avatar</label>
                        <div class="flex items-center gap-5">
                            @if($teacher->avatar)
                                <img src="{{ asset('storage/' . $teacher->avatar) }}" alt="{{ $teacher->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-indigo-200 shadow-xs">
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-xs">
                                    {{ substr($teacher->name, 0, 2) }}
                                </div>
                            @endif

                            <div class="flex-1">
                                <input type="file" name="avatar" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                <p class="text-[11px] text-slate-400 mt-1">JPG, PNG, or WebP. Max size: 2MB.</p>
                                @error('avatar') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Name & Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $teacher->name) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $teacher->email) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Phone & Qualifications -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $teacher->phone) }}" placeholder="+855 12 345 678" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="qualifications" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Academic Credentials / Degrees</label>
                            <input type="text" id="qualifications" name="qualifications" value="{{ old('qualifications', $teacher->qualifications) }}" placeholder="e.g. M.Sc. Computer Science, IELTS 8.0" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('qualifications') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Biography -->
                    <div>
                        <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Teacher Biography / Background</label>
                        <textarea id="bio" name="bio" rows="4" placeholder="Brief summary of your teaching experience, pedagogical focus, and specializations..." class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('bio', $teacher->bio) }}</textarea>
                        @error('bio') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Credential Management (Password) Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="font-extrabold text-slate-900 text-base">Security & Password Management</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Leave blank if you do not wish to change your password.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Current Password</label>
                            <input type="password" id="current_password" name="current_password" autocomplete="current-password" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('current_password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">New Password</label>
                                <input type="password" id="password" name="password" autocomplete="new-password" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Confirm New Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Actions -->
                <div class="flex justify-end gap-3">
                    <button type="submit" class="px-7 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save Profile Changes</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
