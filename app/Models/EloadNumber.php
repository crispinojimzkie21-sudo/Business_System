<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EloadNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'eload_id',
        'number',
        'network',
        'status',
        'description',
        'provider',
        'number_type',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the transactions for this eload number.
     */
    public function transactions()
    {
        return $this->hasMany(EloadTransaction::class);
    }

    /**
     * Check if eload number is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}

