<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    /**
     * Display the Gradebook overview for the teacher's classes.
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $classId = $request->query('class_id');

        $myClasses = ClassRoom::where('teacher_id', $teacher->id)
            ->with('course')
            ->orderBy('name')
            ->get();

        $myClassIds = $myClasses->pluck('id');

        $selectedClass = null;
        $assessments = collect();
        $classAverage = null;
        $highestScore = null;
        $canEditScore = $teacher->hasPermission('edit-score') || $teacher->hasRole('admin');

        if ($classId) {
            $selectedClass = $myClasses->firstWhere('id', (int) $classId);
            abort_unless($selectedClass, 403, 'Unauthorized class section.');

            // Group grades by title and assessment_date to represent unique assessment events
            $allGrades = Grade::where('class_id', $selectedClass->id)
                ->with('student')
                ->orderByDesc('assessment_date')
                ->get();

            $assessments = $allGrades->groupBy('title')->map(function ($gradesGroup, $title) {
                $first = $gradesGroup->first();
                $avgPercentage = round($gradesGroup->avg('percentage'), 1);

                return (object) [
                    'title' => $title,
                    'type' => $first->assessment_type,
                    'date' => $first->assessment_date,
                    'max_score' => $first->max_score,
                    'count' => $gradesGroup->count(),
                    'average' => $avgPercentage,
                    'highest' => $gradesGroup->max('score'),
                    'lowest' => $gradesGroup->min('score'),
                    'grades' => $gradesGroup,
                ];
            });

            if ($allGrades->isNotEmpty()) {
                $classAverage = round($allGrades->avg('percentage'), 1);
                $highestScore = $allGrades->max('score');
            }
        }

        return view('teacher.grades.index', compact(
            'myClasses',
            'selectedClass',
            'assessments',
            'classId',
            'classAverage',
            'highestScore',
            'canEditScore'
        ));
    }

    /**
     * Show form to record a new assessment and scores for a class.
     */
    public function create(Request $request): View
    {
        $teacher = $request->user();
        abort_unless($teacher->hasPermission('edit-score') || $teacher->hasRole('admin'), 403, 'Permission edit-score required.');

        $classId = $request->query('class_id');

        $myClasses = ClassRoom::where('teacher_id', $teacher->id)
            ->with('course')
            ->orderBy('name')
            ->get();

        $selectedClass = null;
        $students = collect();

        if ($classId) {
            $selectedClass = $myClasses->firstWhere('id', (int) $classId);
            abort_unless($selectedClass, 403, 'Unauthorized class section.');
            $students = $selectedClass->students()->orderBy('name')->get();
        }

        return view('teacher.grades.create', compact('myClasses', 'selectedClass', 'students', 'classId'));
    }

    /**
     * Store assessment scores for all students in a class.
     */
    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();
        abort_unless($teacher->hasPermission('edit-score') || $teacher->hasRole('admin'), 403, 'Permission edit-score required.');

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'assessment_type' => ['required', 'in:Exam,Quiz,Assignment,Project,Practical'],
            'max_score' => ['required', 'numeric', 'min:1'],
            'assessment_date' => ['required', 'date'],
            'scores' => ['required', 'array'],
            'scores.*.student_id' => ['required', 'exists:users,id'],
            'scores.*.score' => ['required', 'numeric', 'min:0'],
            'scores.*.feedback' => ['nullable', 'string', 'max:500'],
        ]);

        $class = ClassRoom::where('id', $validated['class_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        foreach ($validated['scores'] as $item) {
            // Cap score to max_score
            $score = min((float) $item['score'], (float) $validated['max_score']);

            Grade::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'student_id' => $item['student_id'],
                    'title' => $validated['title'],
                ],
                [
                    'assessment_type' => $validated['assessment_type'],
                    'score' => $score,
                    'max_score' => $validated['max_score'],
                    'feedback' => $item['feedback'] ?? null,
                    'assessment_date' => $validated['assessment_date'],
                    'created_by' => $teacher->id,
                ]
            );
        }

        return redirect()->route('teacher.grades.index', ['class_id' => $class->id])
            ->with('success', "Assessment '{$validated['title']}' recorded successfully with student scores.");
    }

    /**
     * Show form to edit scores of an assessment event.
     */
    public function edit(Request $request, ClassRoom $class, string $title): View
    {
        $teacher = $request->user();
        abort_unless($teacher->hasPermission('edit-score') || $teacher->hasRole('admin'), 403, 'Permission edit-score required.');
        abort_unless($class->teacher_id === $teacher->id, 403, 'Unauthorized class section.');

        $decodedTitle = urldecode($title);

        $grades = Grade::where('class_id', $class->id)
            ->where('title', $decodedTitle)
            ->with('student')
            ->get();

        abort_if($grades->isEmpty(), 404, 'Assessment not found.');

        $first = $grades->first();

        return view('teacher.grades.edit', compact('class', 'decodedTitle', 'first', 'grades'));
    }

    /**
     * Update scores and constructive feedback for an assessment event.
     */
    public function update(Request $request, ClassRoom $class, string $title): RedirectResponse
    {
        $teacher = $request->user();
        abort_unless($teacher->hasPermission('edit-score') || $teacher->hasRole('admin'), 403, 'Permission edit-score required.');
        abort_unless($class->teacher_id === $teacher->id, 403, 'Unauthorized class section.');

        $decodedTitle = urldecode($title);

        $validated = $request->validate([
            'new_title' => ['required', 'string', 'max:255'],
            'assessment_type' => ['required', 'in:Exam,Quiz,Assignment,Project,Practical'],
            'max_score' => ['required', 'numeric', 'min:1'],
            'assessment_date' => ['required', 'date'],
            'scores' => ['required', 'array'],
            'scores.*.grade_id' => ['required', 'exists:grades,id'],
            'scores.*.score' => ['required', 'numeric', 'min:0'],
            'scores.*.feedback' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated['scores'] as $item) {
            $grade = Grade::where('id', $item['grade_id'])
                ->where('class_id', $class->id)
                ->first();

            if ($grade) {
                $score = min((float) $item['score'], (float) $validated['max_score']);
                $grade->update([
                    'title' => $validated['new_title'],
                    'assessment_type' => $validated['assessment_type'],
                    'score' => $score,
                    'max_score' => $validated['max_score'],
                    'feedback' => $item['feedback'] ?? null,
                    'assessment_date' => $validated['assessment_date'],
                ]);
            }
        }

        return redirect()->route('teacher.grades.index', ['class_id' => $class->id])
            ->with('success', "Assessment '{$validated['new_title']}' updated successfully.");
    }

    /**
     * Remove an entire assessment event from a class.
     */
    public function destroy(Request $request, ClassRoom $class, string $title): RedirectResponse
    {
        $teacher = $request->user();
        abort_unless($teacher->hasPermission('edit-score') || $teacher->hasRole('admin'), 403, 'Permission edit-score required.');
        abort_unless($class->teacher_id === $teacher->id, 403, 'Unauthorized class section.');

        $decodedTitle = urldecode($title);

        Grade::where('class_id', $class->id)
            ->where('title', $decodedTitle)
            ->delete();

        return redirect()->route('teacher.grades.index', ['class_id' => $class->id])
            ->with('success', "Assessment '{$decodedTitle}' deleted successfully.");
    }
}
