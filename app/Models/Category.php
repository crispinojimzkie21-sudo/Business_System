<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'eload_categories';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the eloads for this category.
     */
    public function eloads()
    {
        return $this->hasMany(Eload::class);
    }

    /**
     * Check if category is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}

