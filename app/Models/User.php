<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Enums\AttendanceStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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
        'supervisor_id',
        'attendance_status',
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
            'role' => UserRole::class,
            'attendance_status' => AttendanceStatus::class,
        ];
    }

    public function profile() { return $this->hasOne(Profile::class); }
    public function supervisor() { return $this->belongsTo(User::class, 'supervisor_id'); }
    public function teamMembers() { return $this->hasMany(User::class, 'supervisor_id'); }
    public function pointages() { return $this->hasMany(Pointage::class); }
    public function justificatifs() { return $this->hasMany(Justificatif::class); }
    public function generatedReports() { return $this->hasMany(Rapport::class, 'generated_by'); }
    public function createdQrTokens() { return $this->hasMany(QrToken::class, 'created_by'); }

    public function isRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
