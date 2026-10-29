<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'code',
        'type',
        'value',
        'start_date',
        'expires_date',
        'usage_limit',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'start_date' => 'datetime',
            'expires_date' => 'datetime',
            'usage_limit' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The specific course this discount applies to (or null if global).
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Invoices where this discount was applied.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'discount_id');
    }

    /**
     * Determine if this discount applies globally to any course.
     */
    public function isGlobal(): bool
    {
        return is_null($this->course_id);
    }

    /**
     * Check if the discount is currently valid and not expired.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->expires_date && $now->gt($this->expires_date)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->invoices()->count() >= $this->usage_limit) {
            return false;
        }

        return true;
    }
}
