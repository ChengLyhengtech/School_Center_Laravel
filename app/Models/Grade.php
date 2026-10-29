<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
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
        'title',
        'assessment_type',
        'score',
        'max_score',
        'feedback',
        'assessment_date',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'assessment_date' => 'date',
        ];
    }

    /**
     * The class this grade belongs to.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * The student who received this grade.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The teacher who recorded this grade.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get calculated score percentage.
     */
    public function getPercentageAttribute(): float
    {
        if ($this->max_score <= 0) {
            return 0.0;
        }

        return round(((float) $this->score / (float) $this->max_score) * 100, 1);
    }

    /**
     * Get equivalent letter grade.
     */
    public function getLetterGradeAttribute(): string
    {
        $pct = $this->percentage;

        if ($pct >= 90) {
            return 'A';
        }
        if ($pct >= 80) {
            return 'B';
        }
        if ($pct >= 70) {
            return 'C';
        }
        if ($pct >= 60) {
            return 'D';
        }

        return 'F';
    }
}
