<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /** 
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Permissions for Teachers
        $editScorePerm = Permission::firstOrCreate(['name' => 'edit-score']);
        $manageAttendancePerm = Permission::firstOrCreate(['name' => 'manage-attendance']);
        $viewRosterPerm = Permission::firstOrCreate(['name' => 'view-roster']);

        $teacherRole->permissions()->syncWithoutDetaching([
            $editScorePerm->id,
            $manageAttendancePerm->id,
            $viewRosterPerm->id,
        ]);

        // 2. Create or Update Official School Profile
        SchoolProfile::firstOrCreate(
            ['email' => 'admissions@clh-center.edu.kh'],
            [
                'name' => 'CLH School & Education Center',
                'tagline' => 'Empowering Minds, Shaping Leaders of Tomorrow',
                'address' => 'Building 48, Preah Monivong Boulevard, Daun Penh, Phnom Penh, Cambodia',
                'phone' => '+855 23 888 999 / +855 12 345 678',
                'social_links' => [
                    'facebook' => 'https://facebook.com/clhschoolcenter',
                    'telegram' => 'https://t.me/clhschoolcenter',
                    'youtube' => 'https://youtube.com/@clhschool',
                ],
            ]
        );

        // 3. Create Seeded Users for all roles
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'Admin Boss',
                'password' => Hash::make('password123'),
                'phone' => '+855 12 999 111',
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $teacher = User::firstOrCreate(
            ['email' => 'teacher@school.com'],
            [
                'name' => 'Prof. Robert Vance',
                'password' => Hash::make('password123'),
                'phone' => '+855 12 555 777',
                'bio' => 'Senior English & Applied Linguistics educator with over 10 years of international teaching experience.',
                'qualifications' => 'M.Ed. Applied Linguistics, Cambridge CELTA Certified',
            ]
        );
        $teacher->roles()->syncWithoutDetaching([$teacherRole->id]);

        $student = User::firstOrCreate(
            ['email' => 'student@school.com'],
            [
                'name' => 'Sokha Phearith',
                'password' => Hash::make('password123'),
                'phone' => '+855 98 123 456',
            ]
        );
        $student->roles()->syncWithoutDetaching([$studentRole->id]);

        $student2 = User::firstOrCreate(
            ['email' => 'dara.chan@school.com'],
            [
                'name' => 'Dara Chan',
                'password' => Hash::make('password123'),
                'phone' => '+855 97 654 321',
            ]
        );
        $student2->roles()->syncWithoutDetaching([$studentRole->id]);

        $parent = User::firstOrCreate(
            ['email' => 'parent@school.com'],
            [
                'name' => 'Mr. Sokha Kim (Guardian)',
                'password' => Hash::make('password123'),
                'phone' => '+855 12 777 888',
            ]
        );
        $parent->roles()->syncWithoutDetaching([$parentRole->id]);
        $student->parents()->syncWithoutDetaching([$parent->id]);

        // 4. Create Sample Courses
        $course1 = Course::firstOrCreate(
            ['title' => 'English for Academic & Career Success'],
            [
                'description' => 'Master advanced conversational fluency, academic writing, and professional workplace communication with certified instructors.',
                'price' => 120.00,
                'start_date' => '2026-11-01',
                'end_date' => '2026-12-30',
            ]
        );

        $course2 = Course::firstOrCreate(
            ['title' => 'Full-Stack Web Development Bootcamp'],
            [
                'description' => 'Build high-performance web applications using modern HTML/CSS, Tailwind, JavaScript, PHP, Laravel, and MySQL database architecture.',
                'price' => 180.00,
                'start_date' => '2026-11-15',
                'end_date' => '2027-02-15',
            ]
        );

        $course3 = Course::firstOrCreate(
            ['title' => 'Data Analytics & AI Essentials'],
            [
                'description' => 'Hands-on practical training covering Python for data science, PowerBI business intelligence dashboards, and AI productivity tools.',
                'price' => 150.00,
                'start_date' => '2026-11-10',
                'end_date' => '2027-01-20',
            ]
        );

        $course4 = Course::firstOrCreate(
            ['title' => 'Executive Leadership & Public Speaking'],
            [
                'description' => 'Develop executive presence, persuasive speech delivery, cross-cultural team leadership, and negotiation prowess.',
                'price' => 110.00,
                'start_date' => '2026-12-01',
                'end_date' => '2027-01-15',
            ]
        );

        // 5. Create Sample Classes
        $class1 = ClassRoom::firstOrCreate(
            ['course_id' => $course1->id, 'name' => 'English Batch A1 (Morning)'],
            [
                'teacher_id' => $teacher->id,
                'room' => 'Hall 101 - Smart Room',
                'days' => 'Mon, Wed, Fri',
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'status' => 'active',
            ]
        );

        $class2 = ClassRoom::firstOrCreate(
            ['course_id' => $course2->id, 'name' => 'Full-Stack Section B (Evening)'],
            [
                'teacher_id' => $admin->id,
                'room' => 'Computer Lab 3',
                'days' => 'Tue, Thu, Sat',
                'start_time' => '17:30:00',
                'end_time' => '19:30:00',
                'status' => 'active',
            ]
        );

        $class3 = ClassRoom::firstOrCreate(
            ['course_id' => $course3->id, 'name' => 'Data Analytics Weekend Batch'],
            [
                'teacher_id' => $teacher->id,
                'room' => 'Computer Lab 1',
                'days' => 'Sat, Sun',
                'start_time' => '13:00:00',
                'end_time' => '16:00:00',
                'status' => 'active',
            ]
        );

        // 6. Enroll Students in Teacher's Class
        $student->enrolledClasses()->syncWithoutDetaching([
            $class1->id => ['enrolled_date' => '2026-10-15'],
        ]);
        $student2->enrolledClasses()->syncWithoutDetaching([
            $class1->id => ['enrolled_date' => '2026-10-16'],
        ]);

        // 7. Seed Initial Attendance Record
        Attendance::firstOrCreate(
            [
                'class_id' => $class1->id,
                'student_id' => $student->id,
                'date' => '2026-10-24',
            ],
            [
                'status' => 'Present',
            ]
        );
        Attendance::firstOrCreate(
            [
                'class_id' => $class1->id,
                'student_id' => $student2->id,
                'date' => '2026-10-24',
            ],
            [
                'status' => 'Late',
            ]
        );

        // 8. Seed Initial Sample Grade
        Grade::firstOrCreate(
            [
                'class_id' => $class1->id,
                'student_id' => $student->id,
                'title' => 'Unit 1 Speaking Assessment',
            ],
            [
                'assessment_type' => 'Exam',
                'score' => 92.50,
                'max_score' => 100.00,
                'feedback' => 'Exceptional vocabulary and pronunciation clarity in presentation.',
                'assessment_date' => '2026-10-20',
                'created_by' => $teacher->id,
            ]
        );
        Grade::firstOrCreate(
            [
                'class_id' => $class1->id,
                'student_id' => $student2->id,
                'title' => 'Unit 1 Speaking Assessment',
            ],
            [
                'assessment_type' => 'Exam',
                'score' => 84.00,
                'max_score' => 100.00,
                'feedback' => 'Solid performance, continue expanding complex connective phrases.',
                'assessment_date' => '2026-10-20',
                'created_by' => $teacher->id,
            ]
        );
    }
}
