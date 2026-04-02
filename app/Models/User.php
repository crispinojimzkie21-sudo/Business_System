<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'position',
        'salary',
        'phone',
        'address',
        'hire_date',
        'department',
        'employee_id',
        'employment_status',
        'access_enabled',
        'notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'salary' => 'decimal:2',
            'access_enabled' => 'boolean',
        ];
    }

    /**
     * Check if user access is enabled.
     */
    public function isAccessEnabled()
    {
        return $this->access_enabled ?? true;
    }

    /**
     * Scope for enabled users only.
     */
    public function scopeEnabled($query)
    {
        return $query->where('access_enabled', true);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is cashier (can process sales).
     */
    public function isCashier()
    {
        return $this->role === 'cashier' || $this->role === 'sales_clerk';
    }

    /**
     * Check if user is sales clerk.
     */
    public function isSalesClerk()
    {
        return $this->role === 'sales_clerk';
    }

    /**
     * Check if user can process sales.
     */
    public function canProcessSales()
    {
        return $this->isCashier();
    }

    /**
     * Check if user is a regular employee.
     */
    public function isEmployee()
    {
        return $this->role === 'employee' || $this->role === 'user';
    }

    /**
     * Check if user is a manager.
     */
    public function isManager()
    {
        return $this->role === 'manager';
    }

    /**
     * Get formatted salary.
     */
    public function getFormattedSalaryAttribute()
    {
        return '₱' . number_format($this->salary, 2);
    }

    /**
     * Get attendance records for this user.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get sales records for this user.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}

