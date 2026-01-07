<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Policies\UserPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\SchedulePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(Schedule::class, SchedulePolicy::class);

        // If Filament is available, add a "Profile" item to the user account menu
        try {
            if (class_exists(\Filament\Facades\Filament::class)) {
                \Filament\Facades\Filament::serving(function () {
                    try {
                        if (class_exists(\Filament\Navigation\UserMenuItem::class)) {
                            \Filament\Facades\Filament::registerUserMenuItems([
                                \Filament\Navigation\UserMenuItem::make('profile')
                                    ->label('Profile')
                                    ->url(route('profile.show'))
                                    ->icon('heroicon-o-user'),
                            ]);
                        }
                    } catch (\Throwable $nested) {
                        // ignore
                    }
                });
            }
        } catch (\Throwable $e) {
            // Non-fatal: ignore if Filament API differs or package not present
        }
    }
}
