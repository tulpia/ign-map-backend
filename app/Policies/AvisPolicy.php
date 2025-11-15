<?php

namespace App\Policies;

use App\Models\Avis;
use App\Models\User;

class AvisPolicy
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
    public function view(User $user, Avis $avis): bool
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
    public function update(User $user, Avis $avis): bool
    {
        return $user->id === $avis->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Avis $avis): bool
    {
        return $this->update($user, $avis);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Avis $avis): bool
    {
        return $this->update($user, $avis);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Avis $avis): bool
    {
        return $this->update($user, $avis);
    }
}
