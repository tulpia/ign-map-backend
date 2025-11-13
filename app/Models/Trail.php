<?php

namespace App\Models;

use App\Enums\Trail\TrailDifficulty;
use App\Observers\TrailObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use phpGPX\phpGPX;

#[ObservedBy([TrailObserver::class])]
class Trail extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'latitude',
        'longitude',
        'trace',
        'distance',
        'difficulty',
        'denivele',
        'time_to_complete'
    ];

    protected $casts = [
        'difficulty' => TrailDifficulty::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(TrailImage::class);
    }

    public static function belongsToUser(string $id, User $user): Trail|null
    {
        return self::where('id', $id)->where('user_id', $user->getAttribute('id'))->first();
    }

    public static function getStatsForTrace(UploadedFile $file): array
    {
        try {
            $gpx = (new phpGPX())->load($file);

            foreach ($gpx->tracks as $track) {
                return [
                    'distance' => $track->stats->distance / 1000,
                    'denivele' => $track->stats->cumulativeElevationGain,
                    'latitude' => $track->stats->startedAtCoords['lat'],
                    'longitude' => $track->stats->startedAtCoords['lng'],
                ];
            }
        } catch (\Throwable $e) {
            Log::error('GPX parsing failed: '.$e->getMessage());
        }

        return [];
    }
}
