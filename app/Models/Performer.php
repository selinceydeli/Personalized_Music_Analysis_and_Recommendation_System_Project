<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Performer extends Model
{
    use HasFactory;

    protected $primaryKey = 'artist_id';
    protected $keyType = 'string';

    public function performerRatings()
    {
        return $this->hasMany(PerformerRating::class, 'artist_id', 'artist_id');
    }

    public function albums() {
        return $this->belongsToMany(Album::class, 'albums', 'artist_id', 'album_id');
    }

    public function songs() {
        return $this->hasMany(Song::class, 'artist_id');
    }
}
