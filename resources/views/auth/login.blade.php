<x-guest-layout>
    <!-- Header Title -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Portal Sign In</h2>
        <p class="text-xs text-slate-500 mt-1">Access your academic records, classes, or administration tools.</p>
    </div>

    <!-- Quick Demo Credentials Helper for 1-Click Testing -->
    <div class="mb-6 p-3 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-xs">
        <p class="font-bold text-indigo-900 flex items-center gap-1.5 mb-2">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Quick Test Credentials (Click to Autofill):</span>
        </p>
        <div class="grid grid-cols-3 gap-1.5">
            <button type="button" onclick="fillCreds('admin@school.com', 'password123')" class="px-2 py-1.5 rounded-lg bg-white hover:bg-indigo-600 hover:text-white border border-indigo-200/80 text-indigo-700 font-semibold text-center transition-colors shadow-2xs">
                👑 Admin
            </button>
            <button type="button" onclick="fillCreds('teacher@school.com', 'password123')" class="px-2 py-1.5 rounded-lg bg-white hover:bg-indigo-600 hover:text-white border border-indigo-200/80 text-indigo-700 font-semibold text-center transition-colors shadow-2xs">
                👨‍🏫 Teacher
            </button>
            <button type="button" onclick="fillCreds('student@school.com', 'password123')" class="px-2 py-1.5 rounded-lg bg-white hover:bg-indigo-600 hover:text-white border border-indigo-200/80 text-indigo-700 font-semibold text-center transition-colors shadow-2xs">
                🎓 Student
            </button>
        </div>
    </div>

    <!-- Session Status Flash -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
            <div class="relative">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    placeholder="e.g., student@school.com"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm placeholder:text-slate-400 transition-colors">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 hover:underline" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm placeholder:text-slate-400 transition-colors">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-xs font-medium text-slate-600">Keep me signed in</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold text-sm shadow-md shadow-indigo-600/30 transition-all hover:-translate-y-0.5 active:translate-y-0">
                Sign In to Dashboard
            </button>
        </div>
    </form>

    <!-- Register Link -->
    @if (Route::has('register'))
        <div class="mt-6 pt-5 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-600">
                New to CLH School Center?
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-700 hover:underline ms-1">
                    Create a Student Account
                </a>
            </p>
        </div>
    @endif

    <script>
        function fillCreds(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>
