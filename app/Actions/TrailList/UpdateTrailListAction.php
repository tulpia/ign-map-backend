<?php

namespace App\Actions\TrailList;

use App\Models\TrailList;

class UpdateTrailListAction
{
    /**
     * Execute the action to update a trail list.
     *
     * @param TrailList $trailList
     * @param array $data
     * @return void
     */
    public function execute(TrailList $trailList, array $data): void
    {
        $trailList->update($data);

        if (isset($data['trails'])) {
            $trailIds = array_map('intval', $data['trails']);
            $trailList->trails()->syncWithoutDetaching($trailIds);
        }
    }
}
