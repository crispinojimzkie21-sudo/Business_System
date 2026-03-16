<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EloadNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'network',
        'status',
        'description',
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

