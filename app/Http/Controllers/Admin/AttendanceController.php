<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display the attendance monitoring interface.
     */
    public function index(Request $request): View
    {
        $classId = $request->query('class_id');
        $date = $request->query('date', now()->toDateString());

        $classes = ClassRoom::with('course')->where('status', 'active')->get();
        $selectedClass = $classId ? ClassRoom::with(['students', 'course'])->find($classId) : $classes->first();

        $existingAttendances = [];
        if ($selectedClass) {
            $existingAttendances = Attendance::where('class_id', $selectedClass->id)
                ->where('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return view('admin.attendance.index', compact('classes', 'selectedClass', 'date', 'existingAttendances'));
    }

    /**
     * Save/record batch daily attendance for a class session.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'date' => ['required', 'date'],
            'attendances' => ['required', 'array'],
            'attendances.*' => ['in:Present,Absent,Late'],
        ]);

        foreach ($validated['attendances'] as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'class_id' => $validated['class_id'],
                    'student_id' => $studentId,
                    'date' => $validated['date'],
                ],
                [
                    'status' => $status,
                ]
            );
        }

        return redirect()->route('admin.attendances.index', [
            'class_id' => $validated['class_id'],
            'date' => $validated['date'],
        ])->with('success', 'Attendance records saved successfully.');
    }
}
