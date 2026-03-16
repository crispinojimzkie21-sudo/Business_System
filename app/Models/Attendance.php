<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'latitude',
        'longitude',
        'location_name',
        'check_in_location',
        'check_out_location',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getHoursWorkedAttribute()
    {
        if (!$this->check_out) {
            return null;
        }

        return $this->check_out->diffInHours($this->check_in);
    }

    public function getFormattedCheckInAttribute()
    {
        return $this->check_in ? $this->check_in->format('h:i A') : 'N/A';
    }

    public function getFormattedCheckOutAttribute()
    {
        return $this->check_out ? $this->check_out->format('h:i A') : 'N/A';
    }

    public function getStatusAttribute()
    {
        if ($this->check_in && !$this->check_out) {
            return 'Checked In';
        } elseif ($this->check_in && $this->check_out) {
            return 'Completed';
        } else {
            return 'Absent';
        }
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }
}
