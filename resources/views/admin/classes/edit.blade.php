<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Edit Class: {{ $class->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Update instructor assignment, timetable, room, or status.</p>
            </div>
            <a href="{{ route('admin.classes.show', $class) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                &larr; Back to Details
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <form action="{{ route('admin.classes.update', $class) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course <span class="text-red-500">*</span></label>
                        <select name="course_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $class->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }} (${{ number_format($course->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Assigned Teacher / Instructor <span class="text-red-500">*</span></label>
                        <select name="teacher_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $class->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }} ({{ $teacher->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Class Name / Batch Title <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $class->name) }}" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Room / Location</label>
                        <input type="text" name="room" value="{{ old('room', $class->room) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Days Schedule</label>
                        <input type="text" name="days" value="{{ old('days', $class->days) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time', substr($class->start_time ?? '', 0, 5)) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time', substr($class->end_time ?? '', 0, 5)) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" {{ old('status', $class->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('status', $class->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $class->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.classes.show', $class) }}" class="px-4 py-2 border rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                        Update Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
