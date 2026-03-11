<?php

namespace App\Policies;

use App\Models\TrailList;
use App\Models\User;

class TrailListPolicy
{
    /**
     * Determine whether the user can view any models.
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param TrailList $trailList
     * @return bool
     */
    public function view(User $user, TrailList $trailList): bool
    {
        return $user->id === $trailList->user->id;
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
     * @param TrailList $trailList
     * @return bool
     */
    public function update(User $user, TrailList $trailList): bool
    {
        return $this->view($user, $trailList);
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param TrailList $trailList
     * @return bool
     */
    public function delete(User $user, TrailList $trailList): bool
    {
        return $this->view($user, $trailList);
    }
}
