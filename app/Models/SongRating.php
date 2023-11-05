<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongRating extends Model
{
    use HasFactory;

    protected $table = 'song_ratings';

    protected $fillable = [
        'rating',
        'username',
        'song_id',
        'date_rated',
    ];

    protected $casts = [
        'date_rated' => 'date',
        'rating' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id', 'id');
    }
}
