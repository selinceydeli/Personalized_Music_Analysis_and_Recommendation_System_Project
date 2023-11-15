<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'song_id' => $this->song_id,
            'name' => $this->name,
            'isrc' => $this->isrc,
            'lyrics' => $this->lyrics,
            'performers' => $this->performers,
            'duration' => $this->duration,
            'tempo' => $this->tempo,
            'key' => $this->key,
            'explicit' => $this->explicit,
            'system_entry_date' => $this->system_entry_date,
            'album_id' => $this->album_id
            // You can include relationships and additional data here
        ];
    }
}