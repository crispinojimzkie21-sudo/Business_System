<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eload extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'network',
        'price',
        'category_id',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Get the category that owns the eload.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the transactions for this eload.
     */
    public function transactions()
    {
        return $this->hasMany(EloadTransaction::class);
    }

    /**
     * Check if eload is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }
}

