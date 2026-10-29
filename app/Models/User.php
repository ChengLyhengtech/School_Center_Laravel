<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'avatar', 'bio', 'qualifications'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Roles assigned to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }

    /**
     * Get the primary/first role name attribute (e.g., $user->role).
     */
    public function getRoleAttribute(): ?string
    {
        return $this->roles->first()?->name;
    }

    /**
     * Determine the dashboard URL / route based on the user's role.
     */
    public function dashboardRoute(): string
    {
        if ($this->hasRole('admin')) {
            return route('admin.dashboard');
        }

        if ($this->hasRole('teacher')) {
            return route('teacher.dashboard');
        }

        return route('student.dashboard');
    }

    /**
     * Check if user has a specific role or any of the given roles.
     *
     * @param  string|array<string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
        }

        return $this->roles->contains('name', $roles);
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param  string|array<string>  ...$roles
     */
    public function hasAnyRole(...$roles): bool
    {
        $flattened = collect($roles)->flatten()->all();

        return $this->hasRole($flattened);
    }

    /**
     * Check if user has a specific permission through any of their roles.
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('name', $permissionName))
            ->exists();
    }

    /**
     * Classes taught by this user (Teacher).
     */
    public function taughtClasses(): HasMany
    {
        return $this->hasMany(ClassRoom::class, 'teacher_id');
    }

    /**
     * Class sections/batches enrolled by this user (Student).
     */
    public function enrolledClasses(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'course_students', 'student_id', 'class_id')
            ->withPivot('id', 'enrolled_date')
            ->withTimestamps();
    }

    /**
     * Course student enrollment records.
     */
    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseStudent::class, 'student_id');
    }

    /**
     * Children linked to this parent.
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_students', 'parent_id', 'student_id')
            ->withTimestamps();
    }

    /**
     * Parents linked to this student.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_students', 'student_id', 'parent_id')
            ->withTimestamps();
    }

    /**
     * Wishlist entries for this user.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }

    /**
     * Courses wishlisted by this user.
     */
    public function wishlistCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'wishlists', 'user_id', 'course_id')
            ->withTimestamps();
    }

    /**
     * Invoices billed to this student.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'student_id');
    }

    /**
     * Invoices created by this staff member.
     */
    public function createdInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    /**
     * Payments recorded by this user/staff.
     */
    public function recordedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'recorded_by');
    }

    /**
     * Payments associated with this student (via invoices).
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,
            Invoice::class,
            'student_id', // Foreign key on invoices table...
            'invoice_id', // Foreign key on payments table...
            'id',         // Local key on users table...
            'id'          // Local key on invoices table...
        );
    }

    /**
     * Courses enrolled by this student (via classes and course_students).
     */
    public function enrolledCourses()
    {
        return Course::whereIn('id', function ($query) {
            $query->select('course_id')
                ->from('classes')
                ->join('course_students', 'classes.id', '=', 'course_students.class_id')
                ->where('course_students.student_id', $this->id);
        });
    }

    /**
     * Dynamic collection accessor for enrolledCourses.
     */
    public function getEnrolledCoursesAttribute()
    {
        return $this->enrolledCourses()->get();
    }

    /**
     * Courses taught by this user (via classes).
     */
    public function taughtCourses()
    {
        return Course::whereIn('id', function ($query) {
            $query->select('course_id')
                ->from('classes')
                ->where('classes.teacher_id', $this->id);
        });
    }

    /**
     * Dynamic collection accessor for taughtCourses.
     */
    public function getTaughtCoursesAttribute()
    {
        return $this->taughtCourses()->get();
    }

    /**
     * Attendance records for this student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    /**
     * Grades received by this user (as student).
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    /**
     * Grades recorded by this user (as teacher).
     */
    public function awardedGrades(): HasMany
    {
        return $this->hasMany(Grade::class, 'created_by');
    }
}
