<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'teacher_id',
        'name',
        'room',
        'days',
        'start_time',
        'end_time',
        'status',
    ];

    /**
     * Get the course that this class belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * The teacher assigned to instruct this class.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Students enrolled in this class.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_students', 'class_id', 'student_id')
            ->withPivot('id', 'enrolled_date')
            ->withTimestamps();
    }

    /**
     * Enrollment pivot records for this class.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseStudent::class, 'class_id');
    }

    /**
     * Attendance records for this class.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    /**
     * Grade & score evaluation records for this class.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'class_id');
    }

    /**
     * Accessor for schedule day matching view references.
     */
    public function getScheduleDayAttribute(): ?string
    {
        return $this->days;
    }

    /**
     * Accessor for schedule time slot matching view references.
     */
    public function getScheduleTimeAttribute(): ?string
    {
        if ($this->start_time && $this->end_time) {
            return substr($this->start_time, 0, 5).' - '.substr($this->end_time, 0, 5);
        }

        return null;
    }
}
