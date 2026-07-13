<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEmailList extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'email_sent',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attendance that owns the email list entry.
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
