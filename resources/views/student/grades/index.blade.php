<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Academic Performance & Scorecard') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Review exam scores, quiz results, project milestones, and constructive instructor feedback.</p>
            </div>
            <div>
                <a href="{{ route('student.classes.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                    My Classes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- KPI Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Academic Average</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-indigo-600 mt-1">
                        {{ $overallAverage !== null ? "{$overallAverage}%" : 'No grades' }}
                    </p>
                    <span class="text-xs font-medium text-slate-500">Across all evaluated tasks</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Top Achievement</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">
                        {{ $highestScore !== null ? "{$highestScore}%" : 'N/A' }}
                    </p>
                    <span class="text-xs font-medium text-slate-500">Highest percentage</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Evaluations Logged</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $totalEvaluations }}</p>
                    <span class="text-xs font-medium text-slate-500">Exams, quizzes & projects</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Grade Breakdown</span>
                    <div class="flex items-center gap-1.5 mt-2">
                        <span class="px-2 py-0.5 rounded text-xs font-extrabold bg-emerald-100 text-emerald-800">A: {{ $letterCounts['A'] }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-extrabold bg-blue-100 text-blue-800">B: {{ $letterCounts['B'] }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-extrabold bg-amber-100 text-amber-800">C: {{ $letterCounts['C'] }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-extrabold bg-rose-100 text-rose-800">D/F: {{ $letterCounts['D'] + $letterCounts['F'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Filter by Class -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <form method="GET" action="{{ route('student.grades.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="w-full sm:w-80">
                        <label for="class_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Filter By Class Section</label>
                        <select id="class_id" name="class_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                            <option value="">All My Classes</option>
                            @foreach($enrolledClasses as $c)
                                <option value="{{ $c->id }}" {{ (string) request('class_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->course->title }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($classId)
                        <a href="{{ route('student.grades.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Clear Filter
                        </a>
                    @endif
                </form>
            </div>

            <!-- Evaluations List -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Recorded Evaluations & Constructive Feedback</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Continuous assessment results with instructor remarks</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">
                                <th class="py-3 px-6">Assessment Details</th>
                                <th class="py-3 px-6">Class Section</th>
                                <th class="py-3 px-6 text-center">Score / Max</th>
                                <th class="py-3 px-6 text-center">Percentage</th>
                                <th class="py-3 px-6 text-center">Grade</th>
                                <th class="py-3 px-6">Instructor Constructive Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($grades as $grade)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                                @if($grade->assessment_type === 'Exam') bg-rose-100 text-rose-700
                                                @elseif($grade->assessment_type === 'Quiz') bg-amber-100 text-amber-800
                                                @elseif($grade->assessment_type === 'Project') bg-purple-100 text-purple-700
                                                @else bg-blue-100 text-blue-700 @endif">
                                                {{ $grade->assessment_type }}
                                            </span>
                                            <span class="font-bold text-sm text-slate-900">{{ $grade->title }}</span>
                                        </div>
                                        <p class="text-[11px] font-mono text-slate-400 mt-1">
                                            Evaluated: {{ $grade->assessment_date?->format('M d, Y') }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-semibold text-slate-800">{{ $grade->class->name }}</div>
                                        <div class="text-[11px] text-indigo-600">{{ $grade->class->course->title }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-center font-mono font-extrabold text-sm text-slate-800">
                                        {{ $grade->score }} / {{ $grade->max_score }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="font-extrabold text-xs {{ $grade->percentage >= 70 ? 'text-emerald-600' : ($grade->percentage >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                            {{ $grade->percentage }}%
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-extrabold 
                                            @if($grade->letter_grade === 'A') bg-emerald-100 text-emerald-800
                                            @elseif($grade->letter_grade === 'B') bg-blue-100 text-blue-800
                                            @elseif($grade->letter_grade === 'C') bg-amber-100 text-amber-800
                                            @elseif($grade->letter_grade === 'D') bg-orange-100 text-orange-800
                                            @else bg-rose-100 text-rose-800 @endif">
                                            {{ $grade->letter_grade }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-slate-600">
                                        @if($grade->feedback)
                                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/60 text-xs italic">
                                                "{{ $grade->feedback }}"
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">No feedback remarks logged.</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        No academic evaluations found for your account.
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
