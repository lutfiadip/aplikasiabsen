<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PEMBIMBING, User::ROLE_ANAK_MAGANG]);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin() || $user->isPembimbing() || $user->id === $attendance->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAnakMagang();
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }
}
