<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Performer extends Model
{
    use HasFactory;



    public function performerRatings()
    {
        return $this->hasMany(PerformerRating::class, 'artist_id', 'artist_id');
    }

    public function albums()
    {
        return $this->hasMany(Album::class, 'artist_id');
    }
}
