<?php

namespace App\Models;

use App\Enums\Trail\TrailDifficulty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'difficulty' => TrailDifficulty::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function belongsToUser(string $id, User $user): Trail|null
    {
        return self::where('id', $id)->where('user_id', $user->getAttribute('id'))->first();
    }
}
