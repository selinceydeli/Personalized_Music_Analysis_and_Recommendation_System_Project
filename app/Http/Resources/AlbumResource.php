<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'album_id' => $this->album_id,
            'name' => $this->name,
            'album_type' => $this->album_type,
            'image_url' => $this->image_url,
            'artist_id' => $this->artist_id,
            'label' => $this->label,
            'copyright' => $this->copyright,
            'release_date' => $this->release_date,
            'total_tracks' => $this->total_tracks,
            'popularity' => $this->popularity,
        ];
    }
}