<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_authenticated_admin_is_redirected_to_admin_dashboard_from_login_and_welcome(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $admin->refresh();

        // Visiting /login while authenticated redirects to admin dashboard
        $responseLogin = $this->actingAs($admin)->get('/login');
        $responseLogin->assertRedirect(route('admin.dashboard'));

        // Visiting / while authenticated redirects to admin dashboard
        $responseHome = $this->actingAs($admin)->get('/');
        $responseHome->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_teacher_is_redirected_to_teacher_dashboard_from_login_and_welcome(): void
    {
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $teacher = User::factory()->create();
        $teacher->roles()->attach($teacherRole->id);
        $teacher->refresh();

        $responseLogin = $this->actingAs($teacher)->get('/login');
        $responseLogin->assertRedirect(route('teacher.dashboard'));

        $responseHome = $this->actingAs($teacher)->get('/');
        $responseHome->assertRedirect(route('teacher.dashboard'));
    }

    public function test_authenticated_student_is_redirected_to_student_dashboard_from_login_register_and_welcome(): void
    {
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $student = User::factory()->create();
        $student->roles()->attach($studentRole->id);
        $student->refresh();

        $responseLogin = $this->actingAs($student)->get('/login');
        $responseLogin->assertRedirect(route('student.dashboard'));

        $responseRegister = $this->actingAs($student)->get('/register');
        $responseRegister->assertRedirect(route('student.dashboard'));

        $responseHome = $this->actingAs($student)->get('/');
        $responseHome->assertRedirect(route('student.dashboard'));
    }

    public function test_welcome_page_renders_school_center_info_for_guest(): void
    {
        SchoolProfile::create([
            'name' => 'Sokha IT & Language Academy',
            'tagline' => 'Empowering Cambodia through Tech Education',
            'email' => 'info@sokha.edu.kh',
            'phone' => '+855 12 333 444',
            'address' => 'Daun Penh, Phnom Penh',
        ]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Sokha IT & Language Academy');
        $response->assertSee('Empowering Cambodia through Tech Education');
        $response->assertSee('Sign In');
    }
}
