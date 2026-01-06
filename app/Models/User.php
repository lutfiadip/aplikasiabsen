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
        'is_active',
        'active_from',
        'active_until',
    ];

    /**
     * Role constants
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PEMBIMBING = 'pembimbing';
    public const ROLE_ANAK_MAGANG = 'anak_magang';

    /**
     * Active helpers
     */
    public function isActive(): bool
    {
        // First respect manual flag
        if (! $this->is_active) {
            return false;
        }

        $now = \Illuminate\Support\Carbon::now();

        if ($this->active_from && $now->lt(\Illuminate\Support\Carbon::parse($this->active_from)->startOfDay())) {
            return false;
        }

        if ($this->active_until && $now->gt(\Illuminate\Support\Carbon::parse($this->active_until)->endOfDay())) {
            return false;
        }

        return true;
    }

    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'active_from' => 'date',
        'active_until' => 'date',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPembimbing(): bool
    {
        return $this->role === self::ROLE_PEMBIMBING;
    }

    public function isAnakMagang(): bool
    {
        return $this->role === self::ROLE_ANAK_MAGANG;
    }
}
