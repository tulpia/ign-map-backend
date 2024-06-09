<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Trail extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'trace',
        'distance',
        'difficulty',
        'denivele',
        'time_to_complete'
    ];

    public function user(): HasOne {
        return $this->hasOne(User::class);
    }
}
