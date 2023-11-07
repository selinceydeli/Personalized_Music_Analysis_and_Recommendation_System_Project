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
            'id' => $this->id,
            'name' => $this->name,
            'publ_date' => $this->publ_date,
            'performers' => $this->performers,
            'song_writer' => $this->song_writer,
            'genre' => $this->genre,
            'recording_type' => $this->recording_type,
            'song_length_seconds' => $this->song_length_seconds,
            'tempo' => $this->tempo,
            'danceability' => $this->danceability,
            'energy' => $this->energy,
            'key' => $this->key,
            'loudness' => $this->loudness,
            'speechiness' => $this->speechiness,
            'acousticness' => $this->acousticness,
            'instrumentalness' => $this->instrumentalness,
            'liveness' => $this->liveness,
            'valence' => $this->valence,
            'mood' => $this->mood,
            'language' => $this->language,
            'system_entry_date' => $this->system_entry_date,
            'album_id' => $this->album_id
            // You can include relationships and additional data here
        ];
    }
}