<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ $course->title }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Tuition: ${{ number_format($course->price, 2) }} &bull; Period: {{ $course->start_date?->format('M d, Y') }} - {{ $course->end_date?->format('M d, Y') }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.classes.create', ['course_id' => $course->id]) }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    + Add New Class Batch
                </a>
                <a href="{{ route('admin.courses.edit', $course) }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                    Edit Course
                </a>
                <a href="{{ route('admin.courses.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Course Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Standard Price</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">${{ number_format($course->price, 2) }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Scheduled Batches</p>
                    <p class="text-xl font-bold text-indigo-600 mt-1">{{ $course->classes->count() }} classes</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Total Enrolled</p>
                    <p class="text-xl font-bold text-emerald-600 mt-1">{{ $course->classes->sum(fn ($c) => $c->students->count()) }} students</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Active Discounts</p>
                    <p class="text-xl font-bold text-amber-600 mt-1">{{ $course->discounts->count() }} promos</p>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-xs font-bold uppercase text-gray-500 tracking-wider mb-2">Description / Syllabus</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $course->description ?? 'No detailed description available.' }}</p>
            </div>

            <!-- Scheduled Classes / Batches -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Classes / Batches for this Course</h3>
                        <p class="text-xs text-gray-500">Physical and online sections scheduled under {{ $course->title }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Batch / Section</th>
                                <th class="px-6 py-4">Instructor</th>
                                <th class="px-6 py-4">Schedule & Room</th>
                                <th class="px-6 py-4">Enrolled Students</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($course->classes as $cls)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.classes.show', $cls) }}" class="font-bold text-gray-900 hover:text-indigo-600">
                                            {{ $cls->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-800">
                                        {{ $cls->teacher->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        <div>Room: {{ $cls->room ?? 'Online' }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $cls->days }} ({{ $cls->start_time }} - {{ $cls->end_time }})</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                            {{ $cls->students->count() }} students
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                            {{ $cls->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($cls->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($cls->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.classes.show', $cls) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View Roster</a>
                                        <a href="{{ route('admin.classes.edit', $cls) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-xs">
                                        No class batches created yet for this course. Click "+ Add New Class Batch" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
