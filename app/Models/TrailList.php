<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrailList extends Model
{
    protected $table = 'lists';
    protected $fillable = ['name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trails(): BelongsToMany
    {
        return $this->belongsToMany(Trail::class, 'trail_lists', 'list_id', 'trail_id')->withTimestamps();
    }
}
