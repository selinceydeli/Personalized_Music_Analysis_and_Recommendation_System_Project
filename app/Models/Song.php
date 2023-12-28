<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Playlist;


class Song extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'song_id';

    public function songRatings()
    {
        return $this->hasMany(SongRating::class, 'song_id', 'song_id');
    }

    public function getAverageRatingAttribute()
    {
        if ($this->songRatings->isNotEmpty()) {
            return $this->songRatings->avg('rating');
        }

        return 0; // Default to 0 if there are no ratings
    }
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'song_playlist', 'song_id', 'playlist_id');
    }
}