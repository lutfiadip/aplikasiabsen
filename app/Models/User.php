<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'mentor_id',
        'is_active',
        'active_from',
        'active_until',
        'avatar',
        'intern_id',
        'division',
        'start_date',
        'end_date',
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
        'mentor_id' => 'int',
        'avatar' => 'string',
        'intern_id' => 'string',
        'division' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
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

    /**
     * Mentor (pembimbing) relationship.
     */
    public function mentor(): ?BelongsTo
    {
        return $this->belongsTo(self::class, 'mentor_id');
    }

    /**
     * Mentees (anak magang) relationship.
     */
    public function mentees(): HasMany
    {
        return $this->hasMany(self::class, 'mentor_id');
    }

    /**
     * Return a public URL for the user's avatar compatible with Filament's header avatar.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        return asset('storage/' . $this->avatar);
    }

    /**
     * Accessor for avatar URL as attribute `avatar_url`.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFilamentAvatarUrl();
    }

    /**
     * Backwards-compatible helpers for other libraries (Jetstream, etc.)
     */
    public function getAvatarUrl(): ?string
    {
        return $this->getFilamentAvatarUrl();
    }

    // Some packages expect `profile_photo_url` attribute
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->getFilamentAvatarUrl();
    }
}
