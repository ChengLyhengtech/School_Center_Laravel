<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Course Catalog Management') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Define course templates, descriptions, standard tuition pricing, and schedules.</p>
            </div>
            <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create New Course
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Search Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                <form method="GET" action="{{ route('admin.courses.index') }}" class="flex items-center space-x-2 w-full max-w-md">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search course title or keywords..." class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 w-full">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs rounded-lg hover:bg-gray-900 transition">Search</button>
                    @if($search)
                        <a href="{{ route('admin.courses.index') }}" class="text-xs text-gray-500 hover:text-gray-800 underline">Reset</a>
                    @endif
                </form>
                <span class="text-xs text-gray-500 font-medium">{{ $courses->total() }} total course offerings</span>
            </div>

            <!-- Course Grid / Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Course Title</th>
                                <th class="px-6 py-4">Tuition Price</th>
                                <th class="px-6 py-4">Duration Period</th>
                                <th class="px-6 py-4">Batches / Sections</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($courses as $course)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.courses.show', $course) }}" class="font-bold text-gray-900 hover:text-indigo-600 text-sm">
                                            {{ $course->title }}
                                        </a>
                                        <p class="text-xs text-gray-400 line-clamp-1 mt-0.5">{{ $course->description ?? 'No description provided.' }}</p>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        ${{ number_format($course->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $course->start_date?->format('M d, Y') }} &mdash; {{ $course->end_date?->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                            {{ $course->classes_count }} scheduled batches
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.courses.show', $course) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            View
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="inline" onsubmit="return confirm('Delete this course? All associated classes and enrollments will be affected.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-xs">
                                        No courses in the catalog yet. Click "Create New Course" to add one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($courses->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $courses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
