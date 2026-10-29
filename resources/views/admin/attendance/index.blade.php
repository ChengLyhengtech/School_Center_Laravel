<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Attendance Monitoring & Recording') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Track daily student attendance per class session (Present, Absent, Late).</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Class & Date Selector Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <form method="GET" action="{{ route('admin.attendances.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="w-full sm:w-72">
                        <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Select Class / Batch</label>
                        <select name="class_id" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClass && $selectedClass->id == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->course->title ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-48">
                        <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Session Date</label>
                        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="w-full sm:w-auto flex items-end pt-4 sm:pt-0">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-900 transition">
                            Load Session
                        </button>
                    </div>
                </form>
            </div>

            @if($selectedClass)
                <!-- Attendance Grid Table Form -->
                <form action="{{ route('admin.attendances.store') }}" method="POST" class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                    <input type="hidden" name="date" value="{{ $date }}">

                    <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">{{ $selectedClass->name }}</h3>
                            <p class="text-xs text-gray-500">Session on {{ date('l, F d, Y', strtotime($date)) }} &bull; Teacher: {{ $selectedClass->teacher->name ?? 'Unassigned' }}</p>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                            Save Attendance Records
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Student</th>
                                    <th class="px-6 py-4">Mark Attendance Status</th>
                                    <th class="px-6 py-4 text-right">Current Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($selectedClass->students as $index => $student)
                                    @php
                                        $currentStatus = $existingAttendances[$student->id]->status ?? 'Present';
                                    @endphp
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-6 py-4 text-xs font-mono text-gray-400">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                                {{ strtoupper(substr($student->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-900">{{ $student->name }}</span>
                                                <p class="text-xs text-gray-400">{{ $student->email }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <!-- Present -->
                                                <label class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-lg border border-gray-200 hover:bg-emerald-50 cursor-pointer text-xs">
                                                    <input type="radio" name="attendances[{{ $student->id }}]" value="Present" {{ $currentStatus === 'Present' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                                    <span class="font-semibold text-emerald-700">Present</span>
                                                </label>
                                                <!-- Absent -->
                                                <label class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-lg border border-gray-200 hover:bg-red-50 cursor-pointer text-xs">
                                                    <input type="radio" name="attendances[{{ $student->id }}]" value="Absent" {{ $currentStatus === 'Absent' ? 'checked' : '' }} class="text-red-600 focus:ring-red-500">
                                                    <span class="font-semibold text-red-700">Absent</span>
                                                </label>
                                                <!-- Late -->
                                                <label class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-lg border border-gray-200 hover:bg-amber-50 cursor-pointer text-xs">
                                                    <input type="radio" name="attendances[{{ $student->id }}]" value="Late" {{ $currentStatus === 'Late' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                                                    <span class="font-semibold text-amber-700">Late</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                {{ $currentStatus === 'Present' ? 'bg-emerald-100 text-emerald-800' : ($currentStatus === 'Late' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $currentStatus }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-xs">
                                            No students enrolled in this class yet. Go to <a href="{{ route('admin.classes.show', $selectedClass) }}" class="text-indigo-600 underline">Class Roster</a> to place students first.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($selectedClass->students->isNotEmpty())
                        <div class="p-4 border-t border-gray-100 flex justify-end bg-gray-50/30">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                Save Attendance Records
                            </button>
                        </div>
                    @endif
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
