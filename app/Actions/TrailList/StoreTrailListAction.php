<?php

namespace App\Actions\TrailList;

use App\Models\TrailList;
use App\Models\User;

class StoreTrailListAction
{
    /**
     * Execute the action to store a new trail list.
     *
     * @param TrailList $trailList
     * @return TrailList
     */
    public function execute(TrailList $trailList, User $user): TrailList
    {
        return $user->lists()->create($trailList->attributesToArray());
    }
}
