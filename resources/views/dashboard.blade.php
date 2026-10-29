<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div>
                        {{ __("You're logged in!") }}
                    </div>

                    <div class="border-t pt-4">
                        @if(auth()->user()->hasRole('admin'))
                            <!-- Show Admin Management Tools -->
                            <h3 class="text-2xl font-bold text-indigo-700">Welcome to the Admin Dashboard</h3>
                            <p class="text-gray-600 mt-1">Manage school settings, courses, discounts, and users.</p>
                     <!-- Show Teacher Class & Attendance Tools -->
                            <h3 class="text-2xl font-bold text-emerald-700">Welcome to the Teacher Portal</h3>
                            <p                     @elseif(auth()->user()->hasRole('teacher'))
           class="text-gray-600 mt-1">Manage your assigned classes and student attendance.</p>
                        @elseif(auth()->user()->hasRole('student'))
                            <!-- Show Student Portal -->
                            <h3 class="text-2xl font-bold text-blue-700">Welcome to the Student Portal</h3>
                            <p class="text-gray-600 mt-1">View your enrolled courses and attendance history.</p>
                        @else
                            <!-- Show Customer / Guest Course Browsing -->
                            <h3 class="text-2xl font-bold text-gray-800">Explore Our Courses</h3>
                            <p class="text-gray-600 mt-1">Browse and wishlist courses at our center.</p>
                        @endif
                    </div>

                    <!-- Display the user's role dynamically -->
                    <div class="mt-4 pt-4 border-t flex items-center gap-2">
                        <span class="text-gray-600">Your current role:</span>
                        @forelse(auth()->user()->roles as $role)
                            <span class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-blue-800 bg-blue-100 rounded-full">
                                {{ $role->name }}
                            </span>
                        @empty
                            <span class="px-2.5 py-1 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full">
                                No role assigned
                            </span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
