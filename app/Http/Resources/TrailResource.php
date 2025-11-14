<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'trace' => $this->trace,
            'images' => $this->images->map(fn($img) => [
                'path' => $img->path,
                'url' => $img->url,
            ]),
            'stats' => [
                'denivele' => $this->denivele,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'difficulty' => $this->difficulty,
                'distance' => $this->distance,
                'time_to_complete' => $this->time_to_complete,
            ]
        ];
    }
}
