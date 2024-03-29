<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'artist_id' => $this->artist_id,
            'name' => $this->name,
            'genre' => $this->genre,
            'popularity' => $this->popularity,
            'image_url' => $this->image_url,
        ];
    }
}