<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'price',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Classes (batches/sections) scheduled for this course.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(ClassRoom::class, 'course_id');
    }

    /**
     * Discounts applicable to this course.
     */
    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class, 'course_id');
    }

    /**
     * Wishlist entries for this course.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'course_id');
    }

    /**
     * Users who have added this course to their wishlist.
     */
    public function wishlistedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists', 'course_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Invoice items billing this course.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'course_id');
    }

    /**
     * Students enrolled in any class belonging to this course.
     */
    public function enrollments(): HasManyThrough
    {
        return $this->hasManyThrough(
            CourseStudent::class,
            ClassRoom::class,
            'course_id', // Foreign key on classes table...
            'class_id',  // Foreign key on course_students table...
            'id',        // Local key on courses table...
            'id'         // Local key on classes table...
        );
    }

    /**
     * Alias relationship for student enrollments (compatible with legacy views).
     */
    public function students(): HasManyThrough
    {
        return $this->enrollments();
    }

    /**
     * Attendance records for this course (via classes).
     */
    public function attendances(): HasManyThrough
    {
        return $this->hasManyThrough(
            Attendance::class,
            ClassRoom::class,
            'course_id', // Foreign key on classes table...
            'class_id',  // Foreign key on attendances table...
            'id',        // Local key on courses table...
            'id'         // Local key on classes table...
        );
    }

    /**
     * The primary instructor/teacher assigned to this course (via its classes).
     */
    public function teacher(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            ClassRoom::class,
            'course_id',  // Foreign key on classes table...
            'id',         // Foreign key on users table...
            'id',         // Local key on courses table...
            'teacher_id'  // Local key on classes table...
        );
    }

    /**
     * Scope to filter courses taught by a specific teacher.
     */
    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->whereHas('classes', fn ($q) => $q->where('teacher_id', $teacherId));
    }
}
