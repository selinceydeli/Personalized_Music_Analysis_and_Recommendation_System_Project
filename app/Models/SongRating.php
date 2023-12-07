<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongRating extends Model
{
    use HasFactory;

    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id', 'song_id');
    }
}
