<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('teacher.grades.index', ['class_id' => $class->id]) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">← Back to Gradebook</a>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight mt-1">
                    {{ __('Edit Assessment: ') }} <span class="text-indigo-600">{{ $decodedTitle }}</span>
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Modify scores, maximum marks, or update constructive feedback for your students.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <form method="POST" action="{{ route('teacher.grades.update', ['class' => $class->id, 'title' => urlencode($decodedTitle)]) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Assessment Details Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="font-extrabold text-slate-900 text-base">Assessment Details</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Class Section: <span class="font-bold text-slate-800">{{ $class->name }}</span> ({{ $class->course->title }})</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Assessment Title -->
                        <div class="md:col-span-2">
                            <label for="new_title" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Assessment Title *</label>
                            <input type="text" id="new_title" name="new_title" value="{{ old('new_title', $decodedTitle) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('new_title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="assessment_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Category *</label>
                            <select id="assessment_type" name="assessment_type" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(['Exam', 'Quiz', 'Assignment', 'Project', 'Practical'] as $cat)
                                    <option value="{{ $cat }}" {{ old('assessment_type', $first->assessment_type) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('assessment_type') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Max Score & Date -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="max_score" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Max *</label>
                                <input type="number" id="max_score" name="max_score" value="{{ old('max_score', $first->max_score) }}" min="1" step="any" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('max_score') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="assessment_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Date *</label>
                                <input type="date" id="assessment_date" name="assessment_date" value="{{ old('assessment_date', $first->assessment_date) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <p class="text-xs text-slate-500 mt-0.5">{{ $grades->count() }} graded student(s)</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/50">
                                    <th class="py-3 px-6">Student</th>
                                    <th class="py-3 px-6 w-36">Score</th>
                                    <th class="py-3 px-6">Constructive Feedback</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($grades as $index => $grade)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <input type="hidden" name="scores[{{ $index }}][grade_id]" value="{{ $grade->id }}">
                                            <div class="font-bold text-slate-800">{{ $grade->student->name }}</div>
                                            <div class="text-xs text-slate-400">{{ $grade->student->email }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <input type="number" name="scores[{{ $index }}][score]" value="{{ old("scores.{$index}.score", $grade->score) }}" min="0" step="any" required class="w-full rounded-xl border-slate-300 text-sm font-mono font-bold focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="py-4 px-6">
                                            <input type="text" name="scores[{{ $index }}][feedback]" value="{{ old("scores.{$index}.feedback", $grade->feedback) }}" placeholder="Constructive guidance and notes..." class="w-full rounded-xl border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer -->
                    <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('teacher.grades.index', ['class_id' => $class->id]) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-sm transition-all inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Update Assessment & Scores</span>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
