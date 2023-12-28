<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // Set the primary key to 'username'

    protected $fillable = [
        'playlist_name'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_playlist', 'playlist_id', 'username');
    }

    public function songs()
    {
        return $this->belongsToMany(Song::class, 'song_playlist', 'playlist_id', 'song_id');
    }
}
