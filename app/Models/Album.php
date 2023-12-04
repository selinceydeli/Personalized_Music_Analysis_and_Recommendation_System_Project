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
    public function songs() {
        return $this->hasMany(Song::class, 'album_id');
    }

    public function performers() {
        return $this->belongsToMany(Performer::class, 'albums', 'album_id', 'artist_id');
    }
}
