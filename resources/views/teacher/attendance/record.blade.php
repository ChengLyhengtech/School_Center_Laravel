<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('teacher.attendance.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">← Attendance Hub</a>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight mt-1">
                    {{ __('Daily Attendance Recording') }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Record presence, tardiness, or absences for your class session.</p>
            </div>
            @if($selectedClass)
                <div class="flex items-center gap-2">
                    <a href="{{ route('teacher.attendance.history', ['class_id' => $selectedClass->id]) }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                        View Attendance History
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Selector Card (Class & Date) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <form method="GET" action="{{ route('teacher.attendance.create') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-6">
                        <label for="class_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Select Class Section</label>
                        <select id="class_id" name="class_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 font-medium">
                            <option value="">-- Choose an assigned class --</option>
                            @foreach($myClasses as $c)
                                <option value="{{ $c->id }}" {{ (string) request('class_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->course->title }}) &bull; {{ $c->schedule_day }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label for="date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Session Date</label>
                        <input type="date" id="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 font-medium">
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition-all">
                            Load Roster
                        </button>
                    </div>
                </form>
            </div>

            @if($selectedClass)
                <!-- Attendance Sheet Form -->
                <form method="POST" action="{{ route('teacher.attendance.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                    <input type="hidden" name="date" value="{{ $date }}">

                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <!-- Header Bar with Quick Action -->
                        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">
                                    {{ $selectedClass->name }} Roster &bull; {{ \Carbon\Carbon::parse($date)->format('l, M d, Y') }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $students->count() }} enrolled student(s) &bull; Room: <span class="font-semibold text-slate-700">{{ $selectedClass->room ?? 'TBA' }}</span> &bull; Time: <span class="font-semibold text-slate-700">{{ $selectedClass->schedule_time ?? 'TBA' }}</span>
                                </p>
                            </div>

                            <!-- Bulk Defaults Button -->
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="markAllPresent()" class="px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Mark All Present</span>
                                </button>
                                <button type="button" onclick="markAllAbsent()" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition-all">
                                    Clear
                                </button>
                            </div>
                        </div>

                        <!-- Students Table -->
                        @if($students->isEmpty())
                            <div class="py-16 text-center text-slate-500">
                                No students enrolled in this section yet.
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">
                                            <th class="py-3.5 px-6">#</th>
                                            <th class="py-3.5 px-6">Student Information</th>
                                            <th class="py-3.5 px-6 text-center">Attendance Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-sm">
                                        @foreach($students as $index => $student)
                                            @php
                                                $status = old("attendances.{$index}.status", $existingRecords->get($student->id)?->status ?? 'Present');
                                            @endphp
                                            <tr class="hover:bg-slate-50/60 transition-colors">
                                                <td class="py-4 px-6 text-xs font-mono text-slate-400">
                                                    {{ $index + 1 }}
                                                    <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">
                                                </td>
                                                <td class="py-4 px-6">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                                            {{ substr($student->name, 0, 2) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-800 leading-snug">{{ $student->name }}</div>
                                                            <div class="text-xs text-slate-400">{{ $student->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-6">
                                                    <div class="flex items-center justify-center gap-3">
                                                        <!-- Present -->
                                                        <label class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 has-checked:border-emerald-500 has-checked:bg-emerald-50 has-checked:text-emerald-700 transition-all text-xs font-bold text-slate-600">
                                                            <input type="radio" name="attendances[{{ $index }}][status]" value="Present" {{ $status === 'Present' ? 'checked' : '' }} class="status-present text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                                            <span>Present</span>
                                                        </label>

                                                        <!-- Late -->
                                                        <label class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 has-checked:border-amber-500 has-checked:bg-amber-50 has-checked:text-amber-700 transition-all text-xs font-bold text-slate-600">
                                                            <input type="radio" name="attendances[{{ $index }}][status]" value="Late" {{ $status === 'Late' ? 'checked' : '' }} class="status-late text-amber-500 focus:ring-amber-500 w-4 h-4">
                                                            <span>Late</span>
                                                        </label>

                                                        <!-- Absent -->
                                                        <label class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 has-checked:border-rose-500 has-checked:bg-rose-50 has-checked:text-rose-700 transition-all text-xs font-bold text-slate-600">
                                                            <input type="radio" name="attendances[{{ $index }}][status]" value="Absent" {{ $status === 'Absent' ? 'checked' : '' }} class="status-absent text-rose-600 focus:ring-rose-500 w-4 h-4">
                                                            <span>Absent</span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer Save Button -->
                            <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs text-slate-500">
                                    💡 Tip: Any changes will instantly update the student's attendance records.
                                </span>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Save Attendance Record</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            @else
                <div class="py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                    <p class="font-semibold text-slate-700">Please select an assigned class section above to take attendance.</p>
                </div>
            @endif

        </div>
    </div>

    <script>
        function markAllPresent() {
            document.querySelectorAll('.status-present').forEach(radio => {
                radio.checked = true;
                // Dispatch change event to update parent styling
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        function markAllAbsent() {
            document.querySelectorAll('.status-absent').forEach(radio => {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }
    </script>
</x-app-layout>
