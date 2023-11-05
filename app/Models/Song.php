<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $table = 'songs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'publ_date',
        'performers',
        'song_writer',
        'genre',
        'recording_type',
        'song_length_seconds',
        'tempo',
        'key',
        'mood',
        'language',
        'system_entry_date',
        'album_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publ_date' => 'date',
        'performers' => 'array',
        'song_length_seconds' => 'integer',
        'tempo' => 'decimal:2',
        'system_entry_date' => 'datetime',
    ];

    /**
     * Get the album that the song belongs to.
     */
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'id');
    }

    public function ratings()
    {
        return $this->hasMany(SongRating::class, 'song_id', 'id');
    }
}
