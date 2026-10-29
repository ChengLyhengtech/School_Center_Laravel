<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;

    protected User $student;

    protected User $parent;

    protected Role $teacherRole;

    protected Role $studentRole;

    protected Course $course;

    protected ClassRoom $class;

    protected Permission $editScorePermission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacherRole = Role::create(['name' => 'teacher']);
        $this->studentRole = Role::create(['name' => 'student']);
        $parentRole = Role::create(['name' => 'parent']);
        Role::create(['name' => 'admin']);

        $this->editScorePermission = Permission::create([
            'name' => 'edit-score',
            'description' => 'Can input and edit student scores',
        ]);
        Permission::create(['name' => 'manage-attendance', 'description' => 'Manage attendance']);
        Permission::create(['name' => 'view-roster', 'description' => 'View class rosters']);

        $this->teacherRole->permissions()->attach($this->editScorePermission);

        $this->teacher = User::create([
            'name' => 'Prof. Sokha Seng',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password123'),
            'phone' => '+855 12 777 888',
            'bio' => 'Senior Lead Instructor with 10+ years experience in Full-Stack Web Development.',
            'qualifications' => 'M.Sc. Computer Science',
        ]);
        $this->teacher->roles()->attach($this->teacherRole);

        $this->student = User::create([
            'name' => 'Dara Vichea',
            'email' => 'dara@student.com',
            'password' => Hash::make('password123'),
            'phone' => '+855 12 111 222',
        ]);
        $this->student->roles()->attach($this->studentRole);

        $this->parent = User::create([
            'name' => 'Sophea Vichea (Parent)',
            'email' => 'sophea@parent.com',
            'password' => Hash::make('password123'),
            'phone' => '+855 12 999 111',
        ]);
        $this->parent->roles()->attach($parentRole);

        // Associate parent with student
        $this->parent->children()->attach($this->student->id);

        $this->course = Course::create([
            'title' => 'Web Development Bootcamp',
            'description' => 'Comprehensive masterclass covering Laravel, Vue, MySQL, and REST APIs.',
            'price' => 250.00,
            'duration' => '12 Weeks',
            'start_date' => '2026-10-01',
            'end_date' => '2026-12-31',
            'status' => 'active',
        ]);

        $this->class = ClassRoom::create([
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'name' => 'WDB-2026-Section-A',
            'room' => 'Lab 201',
            'schedule_day' => 'Mon-Wed-Fri',
            'schedule_time' => '08:00 - 10:00',
            'capacity' => 25,
            'status' => 'active',
        ]);

        // Enroll student in class
        $this->class->students()->attach($this->student->id, ['enrolled_date' => now()->toDateString()]);
    }

    public function test_teacher_dashboard_can_be_accessed(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('teacher.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Teacher Portal Dashboard');
        $response->assertSee('WDB-2026-Section-A');
        $response->assertSee('Web Development Bootcamp');
        $response->assertSee('Lab 201');
    }

    public function test_student_cannot_access_teacher_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get(route('teacher.dashboard'));

        $response->assertStatus(403);
    }

    public function test_teacher_can_view_assigned_classes_and_syllabus(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('teacher.classes.index'));
        $response->assertStatus(200);
        $response->assertSee('Assigned Classes & Schedules');
        $response->assertSee('WDB-2026-Section-A');

        $detailResponse = $this->actingAs($this->teacher)->get(route('teacher.classes.show', $this->class));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Course Syllabus');
        $detailResponse->assertSee('Comprehensive masterclass covering Laravel');
        $detailResponse->assertSee('Dara Vichea');
    }

    public function test_teacher_cannot_view_unassigned_class(): void
    {
        $otherTeacher = User::create([
            'name' => 'Other Teacher',
            'email' => 'other@teacher.com',
            'password' => Hash::make('password123'),
        ]);
        $otherTeacher->roles()->attach($this->teacherRole);

        $otherClass = ClassRoom::create([
            'course_id' => $this->course->id,
            'teacher_id' => $otherTeacher->id,
            'name' => 'Unassigned Class',
            'room' => 'Lab 101',
            'schedule_day' => 'Tue-Thu',
            'schedule_time' => '13:00 - 15:00',
            'capacity' => 20,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('teacher.classes.show', $otherClass));
        $response->assertStatus(403);
    }

    public function test_teacher_can_view_student_roster_with_parent_contact(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('teacher.roster.index'));

        $response->assertStatus(200);
        $response->assertSee('Official Student Rosters');
        $response->assertSee('Dara Vichea');
        $response->assertSee('Sophea Vichea (Parent)');
        $response->assertSee('+855 12 999 111');
    }

    public function test_teacher_can_view_student_academic_card(): void
    {
        $response = $this->actingAs($this->teacher)->get(
            route('teacher.roster.show', ['class' => $this->class->id, 'student' => $this->student->id])
        );

        $response->assertStatus(200);
        $response->assertSee('Dara Vichea');
        $response->assertSee('Emergency / Parent Contact');
        $response->assertSee('Sophea Vichea (Parent)');
    }

    public function test_teacher_can_record_daily_attendance_in_bulk(): void
    {
        $today = now()->format('Y-m-d');

        $response = $this->actingAs($this->teacher)->post(route('teacher.attendance.store'), [
            'class_id' => $this->class->id,
            'date' => $today,
            'attendances' => [
                [
                    'student_id' => $this->student->id,
                    'status' => 'Present',
                ],
            ],
        ]);

        $response->assertRedirect(route('teacher.attendance.history', ['class_id' => $this->class->id]));
        $this->assertDatabaseHas('attendances', [
            'class_id' => $this->class->id,
            'student_id' => $this->student->id,
            'status' => 'Present',
        ]);
    }

    public function test_teacher_can_view_attendance_history_and_truancy_alert(): void
    {
        // Seed 3 absences for student to trigger truancy alert
        Attendance::create(['class_id' => $this->class->id, 'student_id' => $this->student->id, 'date' => '2026-10-01', 'status' => 'Absent']);
        Attendance::create(['class_id' => $this->class->id, 'student_id' => $this->student->id, 'date' => '2026-10-02', 'status' => 'Absent']);
        Attendance::create(['class_id' => $this->class->id, 'student_id' => $this->student->id, 'date' => '2026-10-03', 'status' => 'Absent']);

        $response = $this->actingAs($this->teacher)->get(route('teacher.attendance.history', ['class_id' => $this->class->id]));

        $response->assertStatus(200);
        $response->assertSee('Truancy Risk Alert Detected');
        $response->assertSee('Truancy Alert');
    }

    public function test_teacher_with_permission_can_record_new_assessment(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('teacher.grades.store'), [
            'class_id' => $this->class->id,
            'title' => 'Midterm Practical Exam',
            'assessment_type' => 'Exam',
            'max_score' => 100,
            'assessment_date' => now()->format('Y-m-d'),
            'scores' => [
                [
                    'student_id' => $this->student->id,
                    'score' => 88.5,
                    'feedback' => 'Outstanding implementation of MVC architectural patterns.',
                ],
            ],
        ]);

        $response->assertRedirect(route('teacher.grades.index', ['class_id' => $this->class->id]));
        $this->assertDatabaseHas('grades', [
            'class_id' => $this->class->id,
            'student_id' => $this->student->id,
            'title' => 'Midterm Practical Exam',
            'score' => 88.5,
            'max_score' => 100,
            'created_by' => $this->teacher->id,
        ]);
    }

    public function test_teacher_can_update_assessment_scores_and_feedback(): void
    {
        $grade = Grade::create([
            'class_id' => $this->class->id,
            'student_id' => $this->student->id,
            'title' => 'Quiz 1: SQL Optimization',
            'assessment_type' => 'Quiz',
            'score' => 75,
            'max_score' => 100,
            'feedback' => 'Review indexes',
            'assessment_date' => '2026-10-10',
            'created_by' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->put(
            route('teacher.grades.update', ['class' => $this->class->id, 'title' => urlencode('Quiz 1: SQL Optimization')]),
            [
                'new_title' => 'Quiz 1: SQL Optimization (Updated)',
                'assessment_type' => 'Quiz',
                'max_score' => 100,
                'assessment_date' => '2026-10-10',
                'scores' => [
                    [
                        'grade_id' => $grade->id,
                        'score' => 95,
                        'feedback' => 'Mastered query planning after rework.',
                    ],
                ],
            ]
        );

        $response->assertRedirect(route('teacher.grades.index', ['class_id' => $this->class->id]));
        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'title' => 'Quiz 1: SQL Optimization (Updated)',
            'score' => 95,
            'feedback' => 'Mastered query planning after rework.',
        ]);
    }

    public function test_teacher_can_delete_assessment(): void
    {
        Grade::create([
            'class_id' => $this->class->id,
            'student_id' => $this->student->id,
            'title' => 'Temporary Pop Quiz',
            'assessment_type' => 'Quiz',
            'score' => 80,
            'max_score' => 100,
            'assessment_date' => '2026-10-15',
            'created_by' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->delete(
            route('teacher.grades.destroy', ['class' => $this->class->id, 'title' => urlencode('Temporary Pop Quiz')])
        );

        $response->assertRedirect(route('teacher.grades.index', ['class_id' => $this->class->id]));
        $this->assertDatabaseMissing('grades', [
            'class_id' => $this->class->id,
            'title' => 'Temporary Pop Quiz',
        ]);
    }

    public function test_teacher_without_edit_score_permission_is_forbidden_from_recording_scores(): void
    {
        // Detach edit-score permission
        $this->teacherRole->permissions()->detach($this->editScorePermission);

        $response = $this->actingAs($this->teacher)->post(route('teacher.grades.store'), [
            'class_id' => $this->class->id,
            'title' => 'Unauthorized Exam',
            'assessment_type' => 'Exam',
            'max_score' => 100,
            'assessment_date' => now()->format('Y-m-d'),
            'scores' => [
                [
                    'student_id' => $this->student->id,
                    'score' => 90,
                ],
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_can_update_profile_and_credentials(): void
    {
        $response = $this->actingAs($this->teacher)->put(route('teacher.profile.update'), [
            'name' => 'Prof. Sokha Seng (Updated)',
            'email' => 'teacher@school.com',
            'phone' => '+855 12 000 999',
            'qualifications' => 'Ph.D. Software Engineering',
            'bio' => 'Updated faculty bio statement.',
        ]);

        $response->assertRedirect(route('teacher.profile.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $this->teacher->id,
            'name' => 'Prof. Sokha Seng (Updated)',
            'phone' => '+855 12 000 999',
            'qualifications' => 'Ph.D. Software Engineering',
        ]);
    }
}
