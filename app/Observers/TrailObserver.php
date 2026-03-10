<?php

namespace App\Observers;

use App\Models\Trail;
use Illuminate\Support\Facades\Storage;

/**
 * Observer for Trail model.
 * Business logic for creation/update moved to Actions.
 * This remains for pure model-level cleanup side-effects.
 */
class TrailObserver
{
    /**
     * Handle the Trail "deleted" event.
     */
    public function deleted(Trail $trail): void
    {
        if ($trail->trace) {
            Storage::disk('public')->delete($trail->trace);
        }

        foreach ($trail->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }
}
