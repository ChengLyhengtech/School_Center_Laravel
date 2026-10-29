<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'class_id',
        'student_id',
        'date',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * The class batch for this attendance record.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * The student this attendance belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The course associated with this attendance record (via class).
     */
    public function course(): HasOneThrough
    {
        return $this->hasOneThrough(
            Course::class,
            ClassRoom::class,
            'id',         // Foreign key on classes table... (classes.id = attendances.class_id)
            'id',         // Foreign key on courses table... (courses.id = classes.course_id)
            'class_id',   // Local key on attendances table...
            'course_id'   // Local key on classes table...
        );
    }
}
