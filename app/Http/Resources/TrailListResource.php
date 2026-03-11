<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrailListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'trails' => $this->trails->map(function ($trail) {
                return [
                    'id' => $trail->id,
                    'title' => $trail->title,
                    'images' => $trail->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                        ];
                    }),
                ];
            }),
        ];
    }
}
