<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use phpGPX\phpGPX;

/**
 * Service class for handling GPX file operations and statistic extraction.
 */
class GpxService
{
    /**
     * Parse a GPX file and extract core trail statistics.
     *
     * @param UploadedFile $file
     * @return array{distance: float, elevation_gain: float, latitude: float, longitude: float}|array{}
     */
    public function parse(UploadedFile $file): array
    {
        try {
            $gpx = (new phpGPX())->load($file);

            // We assume the first track is the primary one for statistics
            foreach ($gpx->tracks as $track) {
                return [
                    'distance' => (float) ($track->stats->distance / 1000), // Convert to km
                    'elevation_gain' => (float) $track->stats->cumulativeElevationGain,
                    'latitude' => (float) $track->stats->startedAtCoords['lat'],
                    'longitude' => (float) $track->stats->startedAtCoords['lng'],
                ];
            }
        } catch (\Throwable $e) {
            Log::error('GPX parsing failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'exception' => $e
            ]);
        }

        return [];
    }
}
