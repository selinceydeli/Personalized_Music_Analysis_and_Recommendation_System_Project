<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Album extends Model
{
    use HasFactory;

    public function albumRatings()
    {
        return $this->hasMany(albumRating::class, 'album_id', 'album_id');
    }
}
