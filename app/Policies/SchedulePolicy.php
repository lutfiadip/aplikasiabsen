<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAnakMagang();
    }

    public function view(User $user, Schedule $schedule): bool
    {
        return $user->isAdmin() || $user->isAnakMagang();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Schedule $schedule): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Schedule $schedule): bool
    {
        return $user->isAdmin();
    }
}
