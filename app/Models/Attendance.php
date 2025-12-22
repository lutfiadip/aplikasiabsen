<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'schedule_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'latitude',
        'longitude',
        'is_late',
        'notes',
    ];

    protected static function booted()
    {
        // Calculate 'is_late' automatically before saving
        static::saving(function (Attendance $attendance) {
            $attendance->calculateIsLate();
        });
    }

    /**
     * Determine if attendance is late based on schedule and times.
     * Rules:
     * 1) Check-in must be at or before (start_time + grace minutes). If after, it's late (even 1s).
     * 2) Check-out must be strictly greater than end_time; if equal or less, it's late.
     * 3) If either check-in or check-out is late, attendance is late.
     */
    public function calculateIsLate(): void
    {
        $isLate = false;

        $schedule = $this->schedule ?? Schedule::find($this->schedule_id);
        if (! $schedule) {
            // No schedule to compare against
            $this->is_late = false;
            return;
        }

        // reference date is attendance_date
        $date = $this->attendance_date ? Carbon::parse($this->attendance_date)->toDateString() : Carbon::today()->toDateString();

        // Check-in logic
        if ($this->check_in_time) {
            // Normalize to app timezone to avoid timezone artifacts
            $tz = config('app.timezone') ?? date_default_timezone_get();
            $checkIn = Carbon::parse($this->check_in_time)->setTimezone($tz);
            $scheduledStart = Carbon::parse($date.' '.$schedule->start_time, $tz);
            $allowedLatest = $scheduledStart->copy()->addMinutes(intval($schedule->grace_period_minutes ?? 0));

            // If check-in is strictly greater than allowedLatest (by even 1 second), it's late
            if ($checkIn->greaterThan($allowedLatest)) {
                $isLate = true;
            }
        }

        // Check-out logic
        if ($this->check_out_time) {
            // Normalize to app timezone and use check-out's date for comparison if it differs
            $tz = config('app.timezone') ?? date_default_timezone_get();
            $checkOut = Carbon::parse($this->check_out_time, $tz);

            // If the check-out occurred on a different date than attendance_date,
            // compare against the schedule end on the check-out date (handles overnight shifts)
            $scheduledEndDate = $date;
            if ($checkOut->toDateString() !== $date) {
                $scheduledEndDate = $checkOut->toDateString();
            }

            $scheduledEnd = Carbon::parse($scheduledEndDate.' '.$schedule->end_time, $tz);

            // log debug info to help diagnose unexpected cases
            Log::debug('Attendance::calculateIsLate check-out', [
                'attendance_id' => $this->id,
                'attendance_date' => $this->attendance_date,
                'check_out' => $checkOut->toIsoString(),
                'scheduled_end' => $scheduledEnd->toIsoString(),
                'scheduled_end_date' => $scheduledEndDate,
                'schedule_end_time' => $schedule->end_time,
            ]);

            // Mark as late when check-out is less than or equal to scheduled end time
            if (! $checkOut->greaterThan($scheduledEnd)) {
                $isLate = true;
            }
        }

        $this->is_late = $isLate;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
