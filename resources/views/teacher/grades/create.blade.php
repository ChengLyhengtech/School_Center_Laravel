<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('teacher.grades.index', ['class_id' => $classId]) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">← Back to Gradebook</a>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight mt-1">
                    {{ __('Record New Assessment') }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Input exam, quiz, assignment, or project scores with constructive feedback.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Class Selector Form (if not selected) -->
            @if(!$selectedClass)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                    <form method="GET" action="{{ route('teacher.grades.create') }}">
                        <label for="class_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Select Class Section</label>
                        <div class="flex gap-3">
                            <select id="class_id" name="class_id" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                                <option value="">-- Choose an assigned class --</option>
                                @foreach($myClasses as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->name }} ({{ $c->course->title }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="py-2.5 px-6 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs whitespace-nowrap">
                                Continue &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <form method="POST" action="{{ route('teacher.grades.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">

                    <!-- Assessment Details Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-base">Assessment Information</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Assigned Class: <span class="font-bold text-slate-800">{{ $selectedClass->name }}</span> ({{ $selectedClass->course->title }})</p>
                            </div>
                            <a href="{{ route('teacher.grades.create') }}" class="text-xs text-indigo-600 font-semibold hover:underline">Change Class Section</a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Title -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Assessment Title *</label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Midterm Exam, Quiz 1, Final Project" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Assessment Type -->
                            <div>
                                <label for="assessment_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Category *</label>
                                <select id="assessment_type" name="assessment_type" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Exam" {{ old('assessment_type') === 'Exam' ? 'selected' : '' }}>Exam</option>
                                    <option value="Quiz" {{ old('assessment_type') === 'Quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="Assignment" {{ old('assessment_type') === 'Assignment' ? 'selected' : '' }}>Assignment</option>
                                    <option value="Project" {{ old('assessment_type') === 'Project' ? 'selected' : '' }}>Project</option>
                                    <option value="Practical" {{ old('assessment_type') === 'Practical' ? 'selected' : '' }}>Practical</option>
                                </select>
                                @error('assessment_type') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Max Score & Date -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="max_score" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Max *</label>
                                    <input type="number" id="max_score" name="max_score" value="{{ old('max_score', 100) }}" min="1" step="any" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('max_score') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="assessment_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Date *</label>
                                    <input type="date" id="assessment_date" name="assessment_date" value="{{ old('assessment_date', now()->format('Y-m-d')) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('assessment_date') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Students Scoring Grid -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="p-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-base">Student Scores & Constructive Feedback</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Enter numerical score and individualized feedback for each enrolled student.</p>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded bg-indigo-50 text-indigo-700">
                                {{ $students->count() }} Enrolled
                            </span>
                        </div>

                        @if($students->isEmpty())
                            <div class="py-16 text-center text-slate-400">
                                No students currently enrolled in this class.
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/50">
                                            <th class="py-3 px-6">Student</th>
                                            <th class="py-3 px-6 w-36">Score (0 - Max)</th>
                                            <th class="py-3 px-6">Constructive Feedback</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-sm">
                                        @foreach($students as $index => $student)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="py-4 px-6">
                                                    <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $student->id }}">
                                                    <div class="font-bold text-slate-800">{{ $student->name }}</div>
                                                    <div class="text-xs text-slate-400">{{ $student->email }}</div>
                                                </td>
                                                <td class="py-4 px-6">
                                                    <input type="number" name="scores[{{ $index }}][score]" value="{{ old("scores.{$index}.score", 0) }}" min="0" step="any" required class="w-full rounded-xl border-slate-300 text-sm font-mono font-bold focus:border-indigo-500 focus:ring-indigo-500">
                                                </td>
                                                <td class="py-4 px-6">
                                                    <input type="text" name="scores[{{ $index }}][feedback]" value="{{ old("scores.{$index}.feedback") }}" placeholder="e.g. Excellent problem solving; recommend reviewing chapter 4" class="w-full rounded-xl border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Form Actions -->
                            <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('teacher.grades.index', ['class_id' => $selectedClass->id]) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                                    Cancel
                                </a>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Save & Record Evaluation</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            @endif

        </div>
    </div>
</x-app-layout>
