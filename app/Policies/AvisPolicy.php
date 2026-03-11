<?php

namespace App\Policies;

use App\Models\Avis;
use App\Models\User;

class AvisPolicy
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
     * @param Avis $avis
     * @return bool
     */
    public function view(User $user, Avis $avis): bool
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
     * @param Avis $avis
     * @return bool
     */
    public function update(User $user, Avis $avis): bool
    {
        return $user->id === $avis->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param Avis $avis
     * @return bool
     */
    public function delete(User $user, Avis $avis): bool
    {
        return $this->update($user, $avis);
    }

    /**
     * Determine whether the user can restore the model.
     * @param User $user
     * @param Avis $avis
     * @return bool
     */
    public function restore(User $user, Avis $avis): bool
    {
        return $this->update($user, $avis);
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @param User $user
     * @param Avis $avis
     * @return bool
     */
    public function forceDelete(User $user, Avis $avis): bool
    {
        return $this->update($user, $avis);
    }
}
