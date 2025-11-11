<?php

namespace App\Observers;

use App\Models\Trail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TrailObserver
{
    public function creating(Trail $trail): void
    {
        if ($trail->trace instanceof UploadedFile) {
            $stats = Trail::getStatsForTrace($trail->trace);
            $trail->fill($stats);
            $trail->trace = $trail->trace->store('traces');
        }
    }

    /**
     * Handle the Trail "updated" event.
     */
    public function saving(Trail $trail): void
    {
        if ($trail->trace instanceof UploadedFile) {
            $stats = Trail::getStatsForTrace($trail->trace);
            $trail->fill($stats);
            $trail->trace = $trail->trace->store('traces');
        }
    }

    /**
     * Handle the Trail "deleted" event.
     */
    public function deleting(Trail $trail): void
    {
        Storage::delete($trail->trace);
    }
}
