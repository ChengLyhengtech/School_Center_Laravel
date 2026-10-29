<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Role $adminRole;

    protected Role $studentRole;

    protected Role $teacherRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->studentRole = Role::create(['name' => 'student']);
        $this->teacherRole = Role::create(['name' => 'teacher']);
        Role::create(['name' => 'parent']);

        $this->admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@school.com',
            'password' => bcrypt('password123'),
        ]);
        $this->admin->roles()->attach($this->adminRole);
    }

    public function test_admin_dashboard_can_be_accessed(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Executive Admin Center');
    }

    public function test_school_profile_can_be_updated(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.settings.profile.update'), [
            'name' => 'Sokha IT & Language Center',
            'tagline' => 'Bridging Skills to Future',
            'phone' => '+855 12 999 888',
            'email' => 'contact@sokhacenter.edu.kh',
            'address' => 'Russian Federation Blvd, Phnom Penh',
            'facebook' => 'https://facebook.com/sokhait',
            'telegram' => 'https://t.me/sokhait',
        ]);

        $response->assertRedirect(route('admin.settings.profile'));
        $this->assertDatabaseHas('school_profiles', ['name' => 'Sokha IT & Language Center']);
    }

    public function test_admin_can_manage_users_and_link_parent_student(): void
    {
        // 1. Create a Student
        $student = User::create([
            'name' => 'Alice Student',
            'email' => 'alice@test.com',
            'password' => bcrypt('password'),
        ]);
        $student->roles()->attach($this->studentRole);

        // 2. Create a Parent via Admin
        $parent = User::create([
            'name' => 'Bob Parent',
            'email' => 'bob@test.com',
            'password' => bcrypt('password'),
        ]);
        $parentRole = Role::where('name', 'parent')->first();
        $parent->roles()->attach($parentRole);

        // 3. Link student to parent
        $linkResponse = $this->actingAs($this->admin)->post(route('admin.users.link-child', $parent), [
            'student_id' => $student->id,
        ]);
        $linkResponse->assertSessionHas('success');
        $this->assertDatabaseHas('parent_students', [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
        ]);

        // 4. Unlink student from parent
        $unlinkResponse = $this->actingAs($this->admin)->delete(route('admin.users.unlink-child', [$parent, $student]));
        $unlinkResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('parent_students', [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_admin_rbac_role_and_permission_management(): void
    {
        // Create custom permission
        $permResponse = $this->actingAs($this->admin)->post(route('admin.roles-permissions.permissions.store'), [
            'name' => 'edit-score',
        ]);
        $permResponse->assertSessionHas('success');
        $permission = Permission::where('name', 'edit-score')->first();
        $this->assertNotNull($permission);

        // Attach permission to teacher role
        $syncResponse = $this->actingAs($this->admin)->put(route('admin.roles-permissions.roles.update-permissions', $this->teacherRole), [
            'permissions' => [$permission->id],
        ]);
        $syncResponse->assertSessionHas('success');
        $this->assertTrue($this->teacherRole->fresh()->hasPermission('edit-score'));
    }

    public function test_course_and_class_scheduling(): void
    {
        $teacher = User::create([
            'name' => 'Prof. Alan',
            'email' => 'alan@test.com',
            'password' => bcrypt('password'),
        ]);
        $teacher->roles()->attach($this->teacherRole);

        // Create Course
        $courseResponse = $this->actingAs($this->admin)->post(route('admin.courses.store'), [
            'title' => 'Python for AI',
            'description' => 'Deep learning fundamentals',
            'price' => 199.99,
            'start_date' => '2026-11-01',
            'end_date' => '2026-12-31',
        ]);
        $courseResponse->assertRedirect(route('admin.courses.index'));
        $course = Course::where('title', 'Python for AI')->first();
        $this->assertNotNull($course);

        // Create Class Batch
        $classResponse = $this->actingAs($this->admin)->post(route('admin.classes.store'), [
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'AI Batch 1',
            'room' => 'Lab 401',
            'days' => 'Mon-Thu',
            'start_time' => '14:00',
            'end_time' => '16:00',
            'status' => 'active',
        ]);
        $classResponse->assertRedirect(route('admin.classes.index'));
        $this->assertDatabaseHas('classes', [
            'name' => 'AI Batch 1',
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_student_placement_enrollment_and_attendance(): void
    {
        $teacher = User::create([
            'name' => 'Teacher Lee',
            'email' => 'lee@test.com',
            'password' => bcrypt('password'),
        ]);
        $teacher->roles()->attach($this->teacherRole);

        $student = User::create([
            'name' => 'Student Dan',
            'email' => 'dan@test.com',
            'password' => bcrypt('password'),
        ]);
        $student->roles()->attach($this->studentRole);

        $course = Course::create([
            'title' => 'English Writing',
            'price' => 100,
            'start_date' => '2026-11-01',
            'end_date' => '2026-12-31',
        ]);

        $class = ClassRoom::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Writing A1',
            'status' => 'active',
        ]);

        // Place student in class
        $enrollResponse = $this->actingAs($this->admin)->post(route('admin.enrollments.store'), [
            'class_id' => $class->id,
            'student_id' => $student->id,
            'enrolled_date' => '2026-10-24',
        ]);
        $enrollResponse->assertSessionHas('success');
        $this->assertDatabaseHas('course_students', [
            'class_id' => $class->id,
            'student_id' => $student->id,
        ]);

        // Record Daily Attendance
        $attResponse = $this->actingAs($this->admin)->post(route('admin.attendances.store'), [
            'class_id' => $class->id,
            'date' => '2026-10-24',
            'attendances' => [
                $student->id => 'Present',
            ],
        ]);
        $attResponse->assertSessionHas('success');
        $attendance = Attendance::where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->first();
        $this->assertNotNull($attendance);
        $this->assertEquals('2026-10-24', $attendance->date->toDateString());
        $this->assertEquals('Present', $attendance->status);
    }

    public function test_walk_in_invoice_and_installment_payments(): void
    {
        $student = User::create([
            'name' => 'Student WalkIn',
            'email' => 'walkin@test.com',
            'password' => bcrypt('password'),
        ]);
        $student->roles()->attach($this->studentRole);

        $discount = Discount::create([
            'code' => 'FALL10',
            'type' => 'percentage',
            'value' => 10.00,
            'is_active' => true,
        ]);

        // Create Walk-in Invoice with multiple items
        $invoiceResponse = $this->actingAs($this->admin)->post(route('admin.invoices.store'), [
            'student_id' => $student->id,
            'discount_id' => $discount->id,
            'items' => [
                [
                    'description' => 'Tuition Fee - Level 1',
                    'unit_price' => 100.00,
                    'quantity' => 1,
                ],
                [
                    'description' => 'Official Textbook Pack',
                    'unit_price' => 20.00,
                    'quantity' => 1,
                ],
            ],
        ]);

        $invoice = Invoice::where('student_id', $student->id)->latest()->first();
        $this->assertNotNull($invoice);
        // Subtotal: 120, Discount 10%: 12, Total: 108
        $this->assertEquals(120.00, (float) $invoice->subtotal);
        $this->assertEquals(12.00, (float) $invoice->discount_amount);
        $this->assertEquals(108.00, (float) $invoice->total_amount);
        $this->assertEquals('Unpaid', $invoice->status);
        $this->assertEquals($this->admin->id, $invoice->created_by);

        // Record Partial Payment
        $this->actingAs($this->admin)->post(route('admin.payments.store'), [
            'invoice_id' => $invoice->id,
            'amount_paid' => 50.00,
            'payment_method' => 'Cash',
            'reference_number' => 'CASH-REC-1',
            'paid_at' => now()->toDateTimeString(),
        ]);

        $this->assertEquals('Partial', $invoice->fresh()->status);
        $this->assertEquals(58.00, $invoice->fresh()->remainingBalance());

        // Record Remaining Payment
        $this->actingAs($this->admin)->post(route('admin.payments.store'), [
            'invoice_id' => $invoice->id,
            'amount_paid' => 58.00,
            'payment_method' => 'Bank Transfer',
            'reference_number' => 'ABA-TX-002',
            'paid_at' => now()->toDateTimeString(),
        ]);

        $this->assertEquals('Paid', $invoice->fresh()->status);
        $this->assertEquals(0.00, $invoice->fresh()->remainingBalance());

        // Test receipt print route
        $printResponse = $this->actingAs($this->admin)->get(route('admin.invoices.print', $invoice));
        $printResponse->assertStatus(200);
        $printResponse->assertSee($invoice->invoice_number);
    }
}
