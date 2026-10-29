<x-guest-layout>
    <!-- Header Title -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Student Registration</h2>
        <p class="text-xs text-slate-500 mt-1">Enroll online to explore courses, schedules, and learning materials.</p>
    </div>

    <!-- Student Badge Notice -->
    <div class="mb-5 p-3 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-center gap-2.5 text-xs text-blue-900">
        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold shrink-0">
            🎓
        </div>
        <div>
            <span class="font-bold block">Self-Service Student Enrollment</span>
            <span class="text-slate-600 text-[11px]">New accounts are automatically provisioned with student portal privileges.</span>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                placeholder="e.g., Sokha Phearith"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm placeholder:text-slate-400 transition-colors">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                placeholder="e.g., student@example.com"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm placeholder:text-slate-400 transition-colors">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Minimum 8 characters"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm placeholder:text-slate-400 transition-colors">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Repeat your password"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm placeholder:text-slate-400 transition-colors">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <!-- Terms Agreement Notice -->
        <p class="text-[11px] text-slate-500 leading-normal pt-1">
            By registering, you agree to CLH School Center's Academic Terms, Privacy Guidelines, and Student Code of Conduct.
        </p>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold text-sm shadow-md shadow-indigo-600/30 transition-all hover:-translate-y-0.5 active:translate-y-0">
                Complete Registration & Log In
            </button>
        </div>
    </form>

    <!-- Sign In Link -->
    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-600">
            Already registered with CLH Center?
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700 hover:underline ms-1">
                Sign in to your portal
            </a>
        </p>
    </div>
</x-guest-layout>
