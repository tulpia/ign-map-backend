<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'user_id' => $this->user->id,
            'description' => $this->description,
            'trace' => $this->trace,
            'images' => $this->images->map(fn($img) => [
                $img->url
            ]),
            'stats' => [
                'denivele' => $this->denivele,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'difficulty' => $this->difficulty,
                'distance' => $this->distance,
                'time_to_complete' => $this->time_to_complete,
            ],
            'avis' => $this->avis->map(function ($avis) {
                return [
                    'id' => $avis->id,
                    'note' => $avis->note,
                    'description' => $avis->description,
                    'user' => [
                        'id' => $avis->user->id,
                        'name' => $avis->user->name,
                    ],
                    'created_at' => $avis->created_at,
                ];
            }),
            'avis_note' => $this->avis_avg_note ?? $this->avis->avg('note'),
        ];
    }
}
