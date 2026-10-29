<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                {{ __('Student Profile & Parent Connections') }}
            </h2>
            <p class="text-sm text-slate-500 mt-1">Manage personal contact information, account credentials, and review linked guardians.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Profile & Account Settings Form -->
            <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Personal Information Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="font-extrabold text-slate-900 text-base">Personal Learner Details</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Used by instructors and administrative staff to reach you.</p>
                    </div>

                    <!-- Avatar Upload -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Student Photo</label>
                        <div class="flex items-center gap-5">
                            @if($student->avatar)
                                <img src="{{ asset('storage/' . $student->avatar) }}" alt="{{ $student->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-indigo-200 shadow-xs">
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-xs">
                                    {{ substr($student->name, 0, 2) }}
                                </div>
                            @endif

                            <div class="flex-1">
                                <input type="file" name="avatar" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                <p class="text-[11px] text-slate-400 mt-1">JPG, PNG, or WebP. Max size: 2MB.</p>
                                @error('avatar') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Name & Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Full Legal Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 font-medium">
                            @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 font-medium">
                            @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Phone Number & Bio -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Contact Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $student->phone) }}" placeholder="+855 12 345 678" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 font-medium">
                            @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Personal Bio / Goals</label>
                            <input type="text" id="bio" name="bio" value="{{ old('bio', $student->bio) }}" placeholder="e.g. Aspiring Full-Stack Developer" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 font-medium">
                            @error('bio') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Security & Password Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="font-extrabold text-slate-900 text-base">Account Security & Password</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Leave blank if you do not wish to update your login password.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Current Password</label>
                            <input type="password" id="current_password" name="current_password" autocomplete="current-password" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('current_password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">New Password</label>
                                <input type="password" id="password" name="password" autocomplete="new-password" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Confirm New Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-7 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save Profile Changes</span>
                    </button>
                </div>
            </form>

            <!-- Linked Parent / Guardian Visibility Section (Requirement #5) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                        <span>👨‍👩‍👧 Linked Parent / Guardian Visibility</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        These family contacts are officially linked to your student profile via the center's parent-student network. They hold authorized access to review your attendance consistency and financial invoices.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($parents as $parent)
                        <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-sm">{{ $parent->name }}</h4>
                                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-900">
                                        Authorized Guardian
                                    </span>
                                </div>
                                <span class="text-xl">🛡️</span>
                            </div>

                            <div class="mt-4 pt-3 border-t border-amber-200/60 space-y-1 text-slate-700 font-mono">
                                <p><span class="font-sans font-bold text-slate-800">Phone:</span> {{ $parent->phone ?? 'Not provided' }}</p>
                                <p><span class="font-sans font-bold text-slate-800">Email:</span> {{ $parent->email }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            No parent or guardian currently linked to this student record. If you are an underage student, please contact administration to link your parent.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
