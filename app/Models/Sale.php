<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'total_amount',
        'payment_method',
        'customer_name',
        'customer_email',
        'customer_phone',
        'items',
        'user_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTotalAttribute()
    {
        return '₱' . number_format($this->total_amount ?? 0, 2);
    }
    public function getItemsCountAttribute()
    {
        $items = $this->items;
        return is_array($items) ? count($items) : 0;
    }

    public function getTotalQuantityAttribute()
    {
        $items = $this->items;
        if (!is_array($items)) {
            return 0;
        }

        return collect($items)->sum('quantity');
    }

    public function getTransactionIdDisplayAttribute()
    {
        return $this->transaction_id ?? 'N/A';
    }
}
