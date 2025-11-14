<?php

namespace App\Policies;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TrailPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Trail $trail): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Trail $trail): bool
    {
        return $user->id === $trail->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Trail $trail): bool
    {
        return $this->update($user, $trail);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Trail $trail): bool
    {
        return $this->update($user, $trail);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Trail $trail): bool
    {
        return $this->update($user, $trail);
    }
}
