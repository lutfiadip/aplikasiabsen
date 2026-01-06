<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Admin can view all; Pembimbing can view their mentees via filtered queries
        return $user->isAdmin() || $user->isPembimbing();
    }

    public function view(User $user, User $model): bool
    {
        // Admin can view any user. Pembimbing can view only their mentees.
        return $user->isAdmin() || ($user->isPembimbing() && $model->mentor_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        // Only Admin may update users
        return $user->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}
