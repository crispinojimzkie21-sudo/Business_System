<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EloadTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'eload_id',
        'eload_number_id',
        'user_id',
        'eload_number',
        'price',
        'original_price',
        'amount',
        'customer_name',
        'customer_mobile',
        'network',
        'provider',
        'status',
        'transaction_id',
        'reference_number',
        'processed_by',
        'notes',
        'failed_reason',
        'retry_count',
        'discount',
        'commission',
        'created_at',
        'updated_at',
        // Legacy TV E-Load fields
        'mobile_number',
        'transaction_type',
        'customer_contact',
        'provider_response',
        'completed_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
        'status' => 'string',
        'transaction_type' => 'string',
        'provider_response' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the eload that owns the transaction.
     */
    public function eload()
    {
        return $this->belongsTo(Eload::class);
    }

    /**
     * Get the eload number that owns the transaction.
     */
    public function eloadNumber()
    {
        return $this->belongsTo(EloadNumber::class, 'eload_number_id');
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Generate a unique transaction ID.
     */
    public static function generateTransactionId()
    {
        return 'EL-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
    }
}

