<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolProfile;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_profile_can_be_created_and_cast_social_links(): void
    {
        $profile = SchoolProfile::create([
            'name' => 'CLH International Academy',
            'tagline' => 'Excellence in Education',
            'logo' => 'logo.png',
            'banner' => 'banner.jpg',
            'address' => '123 Education Boulevard, Phnom Penh',
            'phone' => '+855 12 345 678',
            'email' => 'info@clh.edu.kh',
            'social_links' => [
                'facebook' => 'https://facebook.com/clh',
                'telegram' => 'https://t.me/clh',
            ],
        ]);

        $this->assertDatabaseHas('school_profiles', ['name' => 'CLH International Academy']);
        $this->assertIsArray($profile->fresh()->social_links);
        $this->assertEquals('https://facebook.com/clh', $profile->fresh()->social_links['facebook']);
    }

    public function test_roles_and_permissions_and_user_rbac(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => 'secret123',
        ]);

        $role = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'manage-courses']);

        $role->permissions()->attach($permission);
        $admin->roles()->attach($role);

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->hasPermission('manage-courses'));
        $this->assertTrue($role->hasPermission('manage-courses'));
        $this->assertFalse($admin->hasRole('teacher'));
    }

    public function test_parent_and_student_relationship(): void
    {
        $parent = User::create([
            'name' => 'Parent User',
            'email' => 'parent@test.com',
            'password' => 'secret123',
        ]);

        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => 'secret123',
        ]);

        $parent->children()->attach($student);

        $this->assertCount(1, $parent->fresh()->children);
        $this->assertEquals($student->id, $parent->fresh()->children->first()->id);
        $this->assertCount(1, $student->fresh()->parents);
        $this->assertEquals($parent->id, $student->fresh()->parents->first()->id);
    }

    public function test_course_classes_and_student_enrollments(): void
    {
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@test.com',
            'password' => 'secret123',
        ]);

        $student = User::create([
            'name' => 'Student Enroll',
            'email' => 'enroll@test.com',
            'password' => 'secret123',
        ]);

        $course = Course::create([
            'title' => 'Web Development Bootcamp',
            'description' => 'Full-stack course',
            'price' => 250.00,
            'start_date' => '2026-11-01',
            'end_date' => '2026-12-31',
        ]);

        $class = ClassRoom::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Cohort 2026-A',
            'room' => 'Lab 3',
            'days' => 'Mon-Wed-Fri',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => 'active',
        ]);

        // Enroll student
        $class->students()->attach($student->id, ['enrolled_date' => '2026-10-24']);

        $this->assertCount(1, $course->fresh()->classes);
        $this->assertEquals($teacher->id, $class->fresh()->teacher->id);
        $this->assertCount(1, $class->fresh()->students);
        $this->assertCount(1, $student->fresh()->enrolledClasses);
        $this->assertCount(1, $course->fresh()->enrollments);

        // Also test ClassModel alias
        $this->assertInstanceOf(ClassRoom::class, new ClassModel);
    }

    public function test_wishlist_functionality(): void
    {
        $user = User::create([
            'name' => 'Wishlist User',
            'email' => 'wish@test.com',
            'password' => 'secret123',
        ]);

        $course = Course::create([
            'title' => 'Data Science 101',
            'price' => 150.00,
            'start_date' => '2026-11-01',
            'end_date' => '2026-12-31',
        ]);

        Wishlist::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->assertCount(1, $user->fresh()->wishlists);
        $this->assertCount(1, $user->fresh()->wishlistCourses);
        $this->assertCount(1, $course->fresh()->wishlistedByUsers);
    }

    public function test_invoicing_discount_and_payment_flow(): void
    {
        $staff = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@test.com',
            'password' => 'secret123',
        ]);

        $student = User::create([
            'name' => 'Billed Student',
            'email' => 'billed@test.com',
            'password' => 'secret123',
        ]);

        $course = Course::create([
            'title' => 'Graphic Design',
            'price' => 200.00,
            'start_date' => '2026-11-01',
            'end_date' => '2026-12-31',
        ]);

        $discount = Discount::create([
            'course_id' => $course->id,
            'code' => 'DISCOUNT20',
            'type' => 'percentage',
            'value' => 20.00,
            'start_date' => now()->subDay(),
            'expires_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        $this->assertTrue($discount->isValid());

        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-0001',
            'student_id' => $student->id,
            'discount_id' => $discount->id,
            'subtotal' => 200.00,
            'discount_amount' => 40.00,
            'total_amount' => 160.00,
            'status' => 'Unpaid',
            'created_by' => $staff->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'course_id' => $course->id,
            'description' => 'Tuition Fee - Graphic Design',
            'unit_price' => 200.00,
            'quantity' => 1,
            'total_price' => 200.00,
        ]);

        $this->assertEquals(0.00, $invoice->totalPaid());
        $this->assertEquals(160.00, $invoice->remainingBalance());
        $this->assertEquals('Unpaid', $invoice->status);

        // Record Partial Payment
        $payment1 = Payment::create([
            'invoice_id' => $invoice->id,
            'amount_paid' => 60.00,
            'payment_method' => 'Cash',
            'reference_number' => 'REC-001',
            'recorded_by' => $staff->id,
            'paid_at' => now(),
        ]);

        $invoice->refresh();
        $this->assertEquals(60.00, $invoice->totalPaid());
        $this->assertEquals(100.00, $invoice->remainingBalance());
        $this->assertEquals('Partial', $invoice->status);

        // Record remaining Payment
        $payment2 = Payment::create([
            'invoice_id' => $invoice->id,
            'amount_paid' => 100.00,
            'payment_method' => 'Bank Transfer',
            'reference_number' => 'REC-002',
            'recorded_by' => $staff->id,
            'paid_at' => now(),
        ]);

        $invoice->refresh();
        $this->assertEquals(160.00, $invoice->totalPaid());
        $this->assertEquals(0.00, $invoice->remainingBalance());
        $this->assertEquals('Paid', $invoice->status);
    }

    public function test_attendance_records(): void
    {
        $teacher = User::create([
            'name' => 'Teacher Att',
            'email' => 'teacheratt@test.com',
            'password' => 'secret123',
        ]);

        $student = User::create([
            'name' => 'Student Att',
            'email' => 'studentatt@test.com',
            'password' => 'secret123',
        ]);

        $course = Course::create([
            'title' => 'Math 101',
            'price' => 100.00,
            'start_date' => '2026-11-01',
            'end_date' => '2026-12-31',
        ]);

        $class = ClassRoom::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Math Section A',
            'status' => 'active',
        ]);

        $attendance = Attendance::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'date' => '2026-11-02',
            'status' => 'Present',
        ]);

        $this->assertEquals('Present', $attendance->status);
        $this->assertEquals($class->id, $attendance->class->id);
        $this->assertEquals($student->id, $attendance->student->id);
        $this->assertCount(1, $class->fresh()->attendances);
        $this->assertCount(1, $student->fresh()->attendances);
    }

    public function test_student_dashboard_displays_correctly(): void
    {
        $studentRole = Role::create(['name' => 'student']);
        $student = User::create([
            'name' => 'Jane Student',
            'email' => 'jane.student@test.com',
            'password' => 'secret123',
        ]);
        $student->roles()->attach($studentRole);

        $teacherRole = Role::create(['name' => 'teacher']);
        $teacher = User::create([
            'name' => 'John Teacher',
            'email' => 'john.teacher@test.com',
            'password' => 'secret123',
        ]);
        $teacher->roles()->attach($teacherRole);

        $course = Course::create([
            'title' => 'Biology 101',
            'price' => 120.00,
            'start_date' => '2026-11-01',
            'end_date' => '2026-12-31',
        ]);

        $class = ClassRoom::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Bio Batch 1',
            'status' => 'active',
        ]);

        $student->enrolledClasses()->attach($class->id, ['enrolled_date' => '2026-11-01']);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-STUDENT-01',
            'student_id' => $student->id,
            'subtotal' => 120.00,
            'total_amount' => 120.00,
            'status' => 'Paid',
            'created_by' => $teacher->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'course_id' => $course->id,
            'description' => 'Tuition fee for Biology 101',
            'quantity' => 1,
            'unit_price' => 120.00,
            'total_price' => 120.00,
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount_paid' => 120.00,
            'payment_method' => 'Cash',
            'recorded_by' => $teacher->id,
        ]);

        Attendance::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'date' => '2026-11-02',
            'status' => 'Present',
        ]);

        $response = $this->actingAs($student)->get(route('student.dashboard'));
        $response->assertOk();
        $response->assertSee('Biology 101');
        $response->assertSee('Present');
        $response->assertSee('120.00');
    }

    public function test_teacher_dashboard_displays_correctly(): void
    {
        $teacherRole = Role::create(['name' => 'teacher']);
        $teacher = User::create([
            'name' => 'Professor Smith',
            'email' => 'prof.smith@test.com',
            'password' => 'secret123',
        ]);
        $teacher->roles()->attach($teacherRole);

        $studentRole = Role::create(['name' => 'student']);
        $student = User::create([
            'name' => 'Alice Student',
            'email' => 'alice@test.com',
            'password' => 'secret123',
        ]);
        $student->roles()->attach($studentRole);

        $course = Course::create([
            'title' => 'Chemistry 101',
            'price' => 150.00,
            'start_date' => '2026-11-01',
            'end_date' => '2026-12-31',
        ]);

        $class = ClassRoom::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Chem Batch A',
            'status' => 'active',
        ]);

        $student->enrolledClasses()->attach($class->id, ['enrolled_date' => '2026-11-01']);

        Attendance::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'date' => '2026-11-02',
            'status' => 'Present',
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher.dashboard'));
        $response->assertOk();
        $response->assertSee('Chemistry 101');
        $response->assertSee('Alice Student');
        $response->assertSee('Present');
    }
}
