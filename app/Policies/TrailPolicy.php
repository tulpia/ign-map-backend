<?php

namespace App\Policies;

use App\Models\Trail;
use App\Models\User;

class TrailPolicy
{
    /**
     * Determine whether the user can view any models.
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param Trail $trail
     * @return bool
     */
    public function view(User $user, Trail $trail): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Trail $trail
     * @return bool
     */
    public function update(User $user, Trail $trail): bool
    {
        return $user->id === $trail->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param Trail $trail
     * @return bool
     */
    public function delete(User $user, Trail $trail): bool
    {
        return $this->update($user, $trail);
    }

    /**
     * Determine whether the user can restore the model.
     * @param User $user
     * @param Trail $trail
     * @return bool
     */
    public function restore(User $user, Trail $trail): bool
    {
        return $this->update($user, $trail);
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @param User $user
     * @param Trail $trail
     * @return bool
     */
    public function forceDelete(User $user, Trail $trail): bool
    {
        return $this->update($user, $trail);
    }
}
