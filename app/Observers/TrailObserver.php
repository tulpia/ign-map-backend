<?php

namespace App\Observers;

use App\Models\Trail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TrailObserver
{
    public function creating(Trail $trail)
    {
        $this->saveTrace($trail);
    }

    public function created(Trail $trail)
    {
        $this->saveImages($trail);
    }

    public function updating(Trail $trail)
    {
        $this->saveTrace($trail);
    }

    public function updated(Trail $trail)
    {
        $this->saveImages($trail);
    }

    public function saveTrace(Trail $trail): void
    {
        if ($trail->trace instanceof UploadedFile) {
            if ($trail->getOriginal('trace')) {
                Storage::delete($trail->getOriginal('trace'));
            }

            $stats = Trail::getStatsForTrace($trail->trace);
            $trail->fill($stats);
            $trail->trace = $trail->trace->store('traces', 'public');
        }
    }

    public function saveImages(Trail $trail): void
    {
        // Ouais on supprime tout, flemme de check
        foreach ($trail->images as $image) {
            Storage::delete($image->path);
            $image->delete();
        }

        $request = request();

        if ($request->hasFile('images') && is_array($request->file('images'))) {
            foreach ($request->file('images') as $image) {
                if ($image instanceof UploadedFile) {
                    $path = $image->store('traces/images', 'public');

                    $trail->images()->create(['path' => $path]);
                }
            }
        }
    }

    /**
     * Handle the Trail "deleted" event.
     */
    public function deleted(Trail $trail): void
    {
        Storage::delete($trail->trace);

        foreach ($trail->images as $image) {
            Storage::delete($image->path);
            $image->delete();
        }
    }
}
