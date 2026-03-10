<?php

namespace App\Actions\Trail;

use App\Models\Trail;
use Illuminate\Support\Facades\DB;

/**
 * Action class for updating an existing Trail with its associated files and statistics.
 */
class UpdateTrailAction extends BaseTrailAction
{
    /**
     * Execute the action to update a trail.
     *
     * @param Trail $trail
     * @param array $data
     * @return Trail
     */
    public function execute(Trail $trail, array $data): Trail
    {
        return DB::transaction(function () use ($trail, $data) {
            $trail->fill($data);

            // Use helpers from BaseTrailAction
            $this->handleTrace($trail, $data['trace'] ?? null);

            $trail->save();

            // Original logic was to replace images on update if new ones were provided
            if (isset($data['images'])) {
                $this->handleImages($trail, $data['images'], true);
            }

            return $trail->load('images');
        });
    }
}
