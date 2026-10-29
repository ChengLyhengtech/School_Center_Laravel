<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Gradebook & Score Management') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Record exams, quizzes, assignments, and evaluate student milestones.</p>
            </div>
            @if($canEditScore && $selectedClass)
                <div>
                    <a href="{{ route('teacher.grades.create', ['class_id' => $selectedClass->id]) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Record New Assessment</span>
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            @if(!$canEditScore)
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-2">
                    <span class="font-bold">Notice:</span> You currently have read-only access to scores. The <span class="font-mono bg-amber-100 px-1.5 py-0.5 rounded">edit-score</span> permission is required from administration to modify evaluations.
                </div>
            @endif

            <!-- Class Section Selector Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <form method="GET" action="{{ route('teacher.grades.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-8">
                        <label for="class_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Select Class Section</label>
                        <select id="class_id" name="class_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                            <option value="">-- Choose an assigned class --</option>
                            @foreach($myClasses as $c)
                                <option value="{{ $c->id }}" {{ (string) request('class_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->course->title }}) &bull; {{ $c->schedule_day }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-4 flex items-center gap-2">
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition-all">
                            Load Gradebook
                        </button>
                    </div>
                </form>
            </div>

            @if($selectedClass)
                <!-- Stats KPI Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Class Average</div>
                        <div class="text-2xl font-extrabold {{ ($classAverage ?? 0) >= 70 ? 'text-emerald-600' : 'text-amber-600' }} mt-1">
                            {{ $classAverage !== null ? "{$classAverage}%" : 'No grades' }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">Across all assessments</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Highest Score</div>
                        <div class="text-2xl font-extrabold text-indigo-600 mt-1">
                            {{ $highestScore !== null ? $highestScore : 'N/A' }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">Top achieved score</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Assessments</div>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">
                            {{ $assessments->count() }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">Exams, quizzes & projects</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Class Section</div>
                        <div class="text-lg font-extrabold text-slate-800 mt-1 truncate">
                            {{ $selectedClass->name }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $selectedClass->course->title }}</div>
                    </div>
                </div>

                <!-- Assessments List -->
                <div class="space-y-6">
                    @forelse($assessments as $item)
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <!-- Assessment Header Card -->
                            <div class="p-5 bg-slate-50/70 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-bold 
                                            @if($item->type === 'Exam') bg-rose-100 text-rose-700
                                            @elseif($item->type === 'Quiz') bg-amber-100 text-amber-800
                                            @elseif($item->type === 'Project') bg-purple-100 text-purple-700
                                            @else bg-blue-100 text-blue-700 @endif">
                                            {{ $item->type }}
                                        </span>
                                        <span class="text-xs font-mono text-slate-500">
                                            {{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-extrabold text-slate-900 mt-1.5">{{ $item->title }}</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Max Score: <span class="font-bold text-slate-700">{{ $item->max_score }}</span> &bull; Average: <span class="font-bold text-emerald-600">{{ $item->average }}%</span> &bull; High: <span class="font-bold text-slate-800">{{ $item->highest }}</span> &bull; Low: <span class="font-bold text-slate-800">{{ $item->lowest }}</span>
                                    </p>
                                </div>

                                <!-- Actions -->
                                @if($canEditScore)
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ route('teacher.grades.edit', ['class' => $selectedClass->id, 'title' => urlencode($item->title)]) }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold transition-all shadow-2xs">
                                            Edit Scores
                                        </a>
                                        <form method="POST" action="{{ route('teacher.grades.destroy', ['class' => $selectedClass->id, 'title' => urlencode($item->title)]) }}" onsubmit="return confirm('Are you sure you want to delete this assessment evaluation? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-all">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Student Scores Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-white">
                                            <th class="py-3 px-6">Student</th>
                                            <th class="py-3 px-6 text-center">Score / Max</th>
                                            <th class="py-3 px-6 text-center">Percentage</th>
                                            <th class="py-3 px-6 text-center">Grade</th>
                                            <th class="py-3 px-6">Teacher Feedback</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-sm">
                                        @foreach($item->grades as $grade)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="py-3.5 px-6">
                                                    <div class="font-bold text-slate-800">{{ $grade->student->name }}</div>
                                                    <div class="text-xs text-slate-400">{{ $grade->student->email }}</div>
                                                </td>
                                                <td class="py-3.5 px-6 text-center font-mono font-bold text-slate-800">
                                                    {{ $grade->score }} / {{ $grade->max_score }}
                                                </td>
                                                <td class="py-3.5 px-6 text-center">
                                                    <span class="font-bold text-xs {{ $grade->percentage >= 70 ? 'text-emerald-600' : ($grade->percentage >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                                        {{ $grade->percentage }}%
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-6 text-center">
                                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-extrabold 
                                                        @if($grade->letter_grade === 'A') bg-emerald-100 text-emerald-800
                                                        @elseif($grade->letter_grade === 'B') bg-blue-100 text-blue-800
                                                        @elseif($grade->letter_grade === 'C') bg-amber-100 text-amber-800
                                                        @elseif($grade->letter_grade === 'D') bg-orange-100 text-orange-800
                                                        @else bg-rose-100 text-rose-800 @endif">
                                                        {{ $grade->letter_grade }}
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-6 text-xs text-slate-600 italic">
                                                    {{ $grade->feedback ?? 'No feedback recorded.' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                            <p class="font-bold text-slate-700">No assessments recorded for this class yet.</p>
                            @if($canEditScore)
                                <p class="text-xs text-slate-400 mt-1">Get started by recording exams, quizzes, or assignments.</p>
                                <div class="mt-4">
                                    <a href="{{ route('teacher.grades.create', ['class_id' => $selectedClass->id]) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold inline-flex items-center gap-1.5 shadow-sm">
                                        + Record First Assessment
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforelse
                </div>
            @else
                <div class="py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                    <p class="font-semibold text-slate-700">Please choose an assigned class section above to view the gradebook.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
