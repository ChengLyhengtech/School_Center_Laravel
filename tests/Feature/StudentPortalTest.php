<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Grade;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $teacher;

    protected User $parent;

    protected Role $studentRole;

    protected Role $teacherRole;

    protected Role $parentRole;

    protected Course $course;

    protected ClassRoom $class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentRole = Role::create(['name' => 'student']);
        $this->teacherRole = Role::create(['name' => 'teacher']);
        $this->parentRole = Role::create(['name' => 'parent']);
        Role::create(['name' => 'admin']);

        $this->teacher = User::create([
            'name' => 'Prof. Sokha Seng',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password123'),
            'phone' => '+855 12 777 888',
            'qualifications' => 'M.Sc. Computer Science',
        ]);
        $this->teacher->roles()->attach($this->teacherRole);

        $this->student = User::create([
            'name' => 'Dara Vichea',
            'email' => 'dara@student.com',
            'password' => Hash::make('password123'),
            'phone' => '+855 12 111 222',
            'bio' => 'Aspiring web developer',
        ]);
        $this->student->roles()->attach($this->studentRole);

        $this->parent = User::create([
            'name' => 'Sophea Vichea (Parent)',
            'email' => 'sophea@parent.com',
            'password' => Hash::make('password123'),
            'phone' => '+855 12 999 111',
        ]);
        $this->parent->roles()->attach($this->parentRole);

        // Associate parent to student
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
            'days' => 'Mon-Wed-Fri',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'status' => 'active',
        ]);

        // Enroll student in class
        $this->class->students()->attach($this->student->id, ['enrolled_date' => now()->toDateString()]);
    }

    public function test_student_dashboard_can_be_accessed(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Student Learning Portal');
        $response->assertSee('WDB-2026-Section-A');
        $response->assertSee('Web Development Bootcamp');
        $response->assertSee('Lab 201');
    }

    public function test_teacher_cannot_access_student_dashboard(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('student.dashboard'));

        $response->assertStatus(403);
    }

    public function test_student_can_view_enrolled_classes_and_schedules(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.classes.index'));

        $response->assertStatus(200);
        $response->assertSee('My Classes');
        $response->assertSee('WDB-2026-Section-A');
        $response->assertSee('Mon-Wed-Fri');
        $response->assertSee('08:00 - 10:00');
        $response->assertSee('Prof. Sokha Seng');

        // Detailed class view
        $detailResponse = $this->actingAs($this->student)->get(route('student.classes.show', $this->class));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Course Description');
        $detailResponse->assertSee('Comprehensive masterclass covering Laravel');
        $detailResponse->assertSee('Prof. Sokha Seng');
    }

    public function test_student_cannot_view_unassigned_class_detail(): void
    {
        $otherClass = ClassRoom::create([
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'name' => 'Other Section B',
            'room' => 'Lab 102',
            'schedule_day' => 'Tue-Thu',
            'schedule_time' => '14:00 - 16:00',
            'capacity' => 20,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.classes.show', $otherClass));

        $response->assertStatus(403);
    }

    public function test_student_can_view_attendance_history_and_consistency(): void
    {
        Attendance::create([
            'class_id' => $this->class->id,
            'student_id' => $this->student->id,
            'date' => '2026-10-10',
            'status' => 'Present',
        ]);

        Attendance::create([
            'class_id' => $this->class->id,
            'student_id' => $this->student->id,
            'date' => '2026-10-12',
            'status' => 'Late',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.attendance.index'));

        $response->assertStatus(200);
        $response->assertSee('Attendance');
        $response->assertSee('Present');
        $response->assertSee('Late');
        $response->assertSee('WDB-2026-Section-A');
    }

    public function test_student_can_view_academic_grades_and_teacher_feedback(): void
    {
        Grade::create([
            'class_id' => $this->class->id,
            'student_id' => $this->student->id,
            'title' => 'Midterm Practical Exam',
            'assessment_type' => 'Exam',
            'score' => 92.5,
            'max_score' => 100,
            'feedback' => 'Exceptional database schema design.',
            'assessment_date' => '2026-10-15',
            'created_by' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->student)->get(route('student.grades.index'));

        $response->assertStatus(200);
        $response->assertSee('Academic Performance');
        $response->assertSee('Midterm Practical Exam');
        $response->assertSee('92.5');
        $response->assertSee('Exceptional database schema design.');
    }

    public function test_student_can_view_invoices_and_itemized_receipt(): void
    {
        $discount = Discount::create([
            'code' => 'PROMO10',
            'type' => 'percentage',
            'value' => 10,
            'start_date' => '2026-10-01',
            'end_date' => '2026-12-31',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-0001',
            'student_id' => $this->student->id,
            'discount_id' => $discount->id,
            'subtotal' => 250.00,
            'discount_amount' => 25.00,
            'total_amount' => 225.00,
            'status' => 'Partial',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'course_id' => $this->course->id,
            'description' => 'Tuition Fee - Web Development Bootcamp',
            'unit_price' => 250.00,
            'quantity' => 1,
            'total_price' => 250.00,
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount_paid' => 100.00,
            'payment_method' => 'Cash',
            'reference_number' => 'REC-99901',
            'paid_at' => now(),
        ]);

        // Test Invoices Index
        $response = $this->actingAs($this->student)->get(route('student.invoices.index'));
        $response->assertStatus(200);
        $response->assertSee('INV-2026-0001');
        $response->assertSee('Partial');
        $response->assertSee('REC-99901');

        // Test Invoice Show
        $showResponse = $this->actingAs($this->student)->get(route('student.invoices.show', $invoice));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('INV-2026-0001');
        $showResponse->assertSee('Tuition Fee - Web Development Bootcamp');
        $showResponse->assertSee('225.00');
    }

    public function test_student_cannot_view_other_students_invoice(): void
    {
        $otherStudent = User::create([
            'name' => 'Other Student',
            'email' => 'other@student.com',
            'password' => Hash::make('password123'),
        ]);
        $otherStudent->roles()->attach($this->studentRole);

        $otherInvoice = Invoice::create([
            'invoice_number' => 'INV-OTHER-99',
            'student_id' => $otherStudent->id,
            'subtotal' => 300.00,
            'discount_amount' => 0.00,
            'total_amount' => 300.00,
            'status' => 'Unpaid',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.invoices.show', $otherInvoice));
        $response->assertStatus(403);
    }

    public function test_student_can_browse_course_catalog(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Course Catalog');
        $response->assertSee('Web Development Bootcamp');
        $response->assertSee('250.00');

        $detailResponse = $this->actingAs($this->student)->get(route('student.catalog.show', $this->course));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Web Development Bootcamp');
        $detailResponse->assertSee('Course Curriculum');
    }

    public function test_student_can_toggle_wishlist(): void
    {
        // 1. Add to wishlist
        $response = $this->actingAs($this->student)->post(route('student.catalog.wishlist.toggle', $this->course));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        // 2. Remove from wishlist
        $toggleAgain = $this->actingAs($this->student)->post(route('student.catalog.wishlist.toggle', $this->course));
        $toggleAgain->assertSessionHas('success');
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);
    }

    public function test_student_can_view_profile_and_linked_parent(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Student Profile');
        $response->assertSee('Dara Vichea');
        $response->assertSee('Sophea Vichea (Parent)');
        $response->assertSee('+855 12 999 111');
    }

    public function test_student_can_update_profile_and_credentials(): void
    {
        $response = $this->actingAs($this->student)->put(route('student.profile.update'), [
            'name' => 'Dara Vichea (Updated)',
            'email' => 'dara@student.com',
            'phone' => '+855 12 333 444',
            'bio' => 'Updated student bio information.',
        ]);

        $response->assertRedirect(route('student.profile.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $this->student->id,
            'name' => 'Dara Vichea (Updated)',
            'phone' => '+855 12 333 444',
            'bio' => 'Updated student bio information.',
        ]);
    }
}
