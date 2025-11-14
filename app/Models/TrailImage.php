<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TrailImage extends Model
{
    protected $fillable = ['trail_id', 'path'];
    protected $appends = ['url'];

    public function trail(): BelongsTo
    {
        return $this->belongsTo(Trail::class);
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }
}
