<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'grace_period_minutes',
        'is_active',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
    //
}
