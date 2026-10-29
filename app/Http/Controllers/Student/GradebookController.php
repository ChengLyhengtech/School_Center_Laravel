<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradebookController extends Controller
{
    /**
     * Display student's academic scores, evaluations, and teacher feedback.
     */
    public function index(Request $request): View
    {
        /** @var User $student */
        $student = $request->user();
        $classId = $request->query('class_id');

        $enrolledClasses = $student->enrolledClasses()->with('course')->get();

        $query = Grade::where('student_id', $student->id)
            ->with(['class.course', 'teacher']);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $grades = $query->orderByDesc('assessment_date')->get();

        $totalEvaluations = $grades->count();
        $overallAverage = $totalEvaluations > 0 ? round($grades->avg('percentage'), 1) : null;
        $highestScore = $grades->max('percentage');

        // Letter grade counts
        $letterCounts = [
            'A' => $grades->filter(fn ($g) => $g->letter_grade === 'A')->count(),
            'B' => $grades->filter(fn ($g) => $g->letter_grade === 'B')->count(),
            'C' => $grades->filter(fn ($g) => $g->letter_grade === 'C')->count(),
            'D' => $grades->filter(fn ($g) => $g->letter_grade === 'D')->count(),
            'F' => $grades->filter(fn ($g) => $g->letter_grade === 'F')->count(),
        ];

        return view('student.grades.index', compact(
            'enrolledClasses',
            'grades',
            'totalEvaluations',
            'overallAverage',
            'highestScore',
            'letterCounts',
            'classId'
        ));
    }
}
