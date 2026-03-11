<?php

namespace App\Actions\Avis;

use App\Models\Avis;
use App\Models\Trail;
use App\Models\User;

class StoreAvisAction
{
    /**
     * Execute the action to store a new Avis.
     *
     * @param Avis $avis
     * @param Trail $trail
     * @param User $user
     * @return void
     */
    public function execute(Avis $avis, Trail $trail, User $user): void
    {
        // On associe le tout
        $avis->trail()->associate($trail);
        $avis->user()->associate($user);
        $avis->save();
    }
}
