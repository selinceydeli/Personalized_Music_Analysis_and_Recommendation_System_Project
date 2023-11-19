<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Song extends Model
{
    use HasFactory;

    protected $primaryKey = 'song_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($song) {
            $song->{$song->getKeyName()} = (string) Str::uuid();
        });
    }

    public function ratings()
    {
        return $this->hasMany(SongRating::class, 'song_id', 'song_id');
    }

    public function getAverageRatingAttribute()
    {
        if ($this->ratings->isNotEmpty()) {
            return $this->ratings->avg('rating');
        }

        return 0; // Default to 0 if there are no ratings
    }
}
