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
        ];
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'manager']);
    }

    /**
     * Get complaints captured by this user
     */
    public function capturedComplaints()
    {
        return $this->hasMany(Complaint::class, 'captured_by');
    }

    /**
     * Get complaints assigned to this user
     */
    public function assignedComplaints()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }

    /**
     * Get activity logs for this user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
