<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'name',
        'album_type',
        'image_url',
        'artist_id',
        'label',
        'copyright',
        'release_date',
        'total_tracks',
        'popularity'
    ];

    public function albumRatings()
    {
        return $this->hasMany(albumRating::class, 'album_id', 'album_id');
    }

    public function performer()
    {
        return $this->belongsTo(Performer::class, 'artist_id');
    }

    public function getAverageRatingAttribute()
    {
        if ($this->albumRatings->isNotEmpty()) {
            return $this->albumRatings->avg('rating');
        }

        return 0; // Default to 0 if there are no ratings
    }
}
