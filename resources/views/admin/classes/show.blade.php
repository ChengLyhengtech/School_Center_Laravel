<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ $class->name }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $class->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($class->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($class->status) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">Course: {{ $class->course->title ?? '-' }} &bull; Teacher: {{ $class->teacher->name ?? 'Unassigned' }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.attendances.index', ['class_id' => $class->id]) }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Mark Attendance
                </a>
                <a href="{{ route('admin.classes.edit', $class) }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                    Edit Class
                </a>
                <a href="{{ route('admin.classes.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Class Quick Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Assigned Teacher</p>
                    <p class="text-base font-bold text-gray-900 mt-1">{{ $class->teacher->name ?? 'Unassigned' }}</p>
                    <p class="text-xs text-gray-400">{{ $class->teacher->email ?? '' }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Room & Location</p>
                    <p class="text-base font-bold text-gray-900 mt-1">{{ $class->room ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-400">Campus physical / virtual facility</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Days & Schedule</p>
                    <p class="text-base font-bold text-gray-900 mt-1">{{ $class->days ?? 'TBA' }}</p>
                    <p class="text-xs text-indigo-600 font-semibold">{{ $class->start_time ?? '-' }} &mdash; {{ $class->end_time ?? '-' }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Enrolled Students</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $class->students->count() }}</p>
                    <p class="text-xs text-gray-400">Active class roster</p>
                </div>
            </div>

            <!-- Student Placement / Quick Enroll Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider mb-2">Student Placement (Direct Class Enrollment)</h3>
                <form action="{{ route('admin.enrollments.store') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $class->id }}">
                    
                    <select name="student_id" required class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 flex-1 w-full">
                        <option value="">-- Select student to place in this batch --</option>
                        @foreach($availableStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                        @endforeach
                    </select>

                    <input type="date" name="enrolled_date" value="{{ date('Y-m-d') }}" required class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 w-full sm:w-44">

                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg transition shrink-0 w-full sm:w-auto">
                        Enroll into Class
                    </button>
                </form>
            </div>

            <!-- Student Roster Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Class Roster (Enrolled Students)</h3>
                        <p class="text-xs text-gray-500">Official student list for {{ $class->name }}</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-gray-100 text-gray-700">
                        {{ $class->students->count() }} Total
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">#</th>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Placement / Enrolled Date</th>
                                <th class="px-6 py-4">Parents / Guardians</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($class->students as $index => $student)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4 text-xs text-gray-400 font-mono">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($student->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $student) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                                {{ $student->name }}
                                            </a>
                                            <p class="text-xs text-gray-400">{{ $student->email }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600 font-mono">
                                        {{ $student->pivot->enrolled_date ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $student->parents->pluck('name')->join(', ') ?: 'None linked' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('admin.enrollments.destroy', $student->pivot->id) }}" method="POST" class="inline" onsubmit="return confirm('Unenroll student from this class?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold">
                                                Unenroll
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-xs">
                                        No students enrolled in this class batch yet. Use the placement form above to add students.
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
