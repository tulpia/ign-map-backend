<?php

namespace App\Actions\Trail;

use App\Models\Trail;
use App\Services\GpxService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Base class for Trail actions to share file and GPX processing logic.
 */
abstract class BaseTrailAction
{
    public function __construct(
        protected GpxService $gpxService
    ) {}

    /**
     * Handle GPX trace upload, parsing, and stat extraction.
     *
     * @param Trail $trail
     * @param UploadedFile|null $traceFile
     * @return void
     */
    protected function handleTrace(Trail $trail, ?UploadedFile $traceFile): void
    {
        if (!$traceFile) {
            return;
        }

        // Delete old trace if it exists (for updates)
        if ($trail->exists && $trail->getOriginal('trace')) {
            Storage::disk('public')->delete($trail->getOriginal('trace'));
        }

        $stats = $this->gpxService->parse($traceFile);

        $trail->fill([
            'distance' => $stats['distance'] ?? ($trail->exists ? $trail->distance : 0),
            'denivele' => $stats['elevation_gain'] ?? ($trail->exists ? $trail->denivele : 0),
            'latitude' => $stats['latitude'] ?? ($trail->exists ? $trail->latitude : null),
            'longitude' => $stats['longitude'] ?? ($trail->exists ? $trail->longitude : null),
        ]);

        $trail->trace = $traceFile->store('traces', 'public');
    }

    /**
     * Handle multiple image uploads.
     *
     * @param Trail $trail
     * @param array|null $images
     * @param bool $deleteExisting
     * @return void
     */
    protected function handleImages(Trail $trail, ?array $images, bool $deleteExisting = false): void
    {
        if ($deleteExisting) {
            foreach ($trail->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        if (!$images) {
            return;
        }

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('traces/images', 'public');
                $trail->images()->create(['path' => $path]);
            }
        }
    }
}
