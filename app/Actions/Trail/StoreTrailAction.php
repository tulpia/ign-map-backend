<?php

namespace App\Actions\Trail;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Action class for creating a new Trail with its associated files and statistics.
 */
class StoreTrailAction extends BaseTrailAction
{
    /**
     * Execute the action to create a trail.
     *
     * @param User $user
     * @param array $data
     * @return Trail
     */
    public function execute(User $user, array $data): Trail
    {
        return DB::transaction(function () use ($user, $data) {
            $trail = new Trail($data);
            $trail->user()->associate($user);

            // Use helpers from BaseTrailAction
            $this->handleTrace($trail, $data['trace'] ?? null);

            $trail->save();

            $this->handleImages($trail, $data['images'] ?? null);

            return $trail;
        });
    }
}
