<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'invoice_id',
        'amount_paid',
        'payment_method',
        'reference_number',
        'recorded_by',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * The invoice this payment belongs to.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * The staff member who recorded this payment.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * The student this payment is associated with (through invoice).
     */
    public function student(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Invoice::class,
            'id',         // Local key on invoice
            'id',         // Local key on user
            'invoice_id', // Foreign key on payment
            'student_id'  // Foreign key on invoice
        );
    }

    /**
     * Accessor for course associated with this payment (via invoice items).
     */
    public function getCourseAttribute(): ?Course
    {
        return $this->invoice?->items?->first()?->course;
    }

    /**
     * Accessor for paid amount matching legacy payment views.
     */
    public function getPaidAmountAttribute(): float
    {
        return (float) ($this->amount_paid ?? 0);
    }

    /**
     * Accessor for status matching legacy payment views.
     */
    public function getStatusAttribute(): string
    {
        return $this->invoice?->status ?? 'Paid';
    }

    /**
     * Accessor for student_id matching legacy queries.
     */
    public function getStudentIdAttribute(): ?int
    {
        return $this->invoice?->student_id;
    }

    /**
     * Scope to filter payments for a given student.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->whereHas('invoice', fn ($q) => $q->where('student_id', $studentId));
    }

    /**
     * The "booted" method of the model.
     * Automatically update parent invoice status whenever a payment is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function (Payment $payment) {
            $payment->invoice?->refreshStatus();
        });

        static::deleted(function (Payment $payment) {
            $payment->invoice?->refreshStatus();
        });
    }
}
