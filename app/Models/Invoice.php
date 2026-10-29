<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'invoice_number',
        'student_id',
        'discount_id',
        'subtotal',
        'discount_amount',
        'total_amount',
        'status',
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
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * The student billed for this invoice.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The discount applied to this invoice, if any.
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    /**
     * The staff member who created this invoice.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Line items on this invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    /**
     * Payment transactions made against this invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    /**
     * Calculate total amount paid so far.
     */
    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount_paid');
    }

    /**
     * Calculate remaining balance due.
     */
    public function remainingBalance(): float
    {
        return max(0.00, (float) $this->total_amount - $this->totalPaid());
    }

    /**
     * Refresh and persist the invoice status based on recorded payments.
     */
    public function refreshStatus(): void
    {
        $paid = $this->totalPaid();

        if ($paid <= 0) {
            $this->status = 'Unpaid';
        } elseif ($paid >= (float) $this->total_amount) {
            $this->status = 'Paid';
        } else {
            $this->status = 'Partial';
        }

        $this->save();
    }
}
