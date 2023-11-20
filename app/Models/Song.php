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
    public function scopeFilter($query, array $filters)
    {
        if ($filters['genre'] ?? false) {
            $requestedGenre = request('genre');
            $genres = explode('/', $requestedGenre);

            $query->where(function ($subquery) use ($genres) {
                foreach ($genres as $genre) {
                    $subquery->orWhere('genre', 'LIKE', '%' . $genre . '%');
                    $subquery->orWhere('genre', 'LIKE', $genre . '%');
                    $subquery->orWhere('genre', 'LIKE', '%' . $genre);
                    $subquery->orWhere('genre', 'LIKE', $genre);
                }
            });
        }

        if ($filters['search'] ?? false) {
            $searchTerm = request('search');

            $query->where(function ($subquery) use ($searchTerm) {
                $subquery->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('genre', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('album', function ($albumSubquery) use ($searchTerm) {
                        $albumSubquery->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('artist', function ($artistSubquery) use ($searchTerm) {
                        $artistSubquery->where('name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        return $query;
    }

    public function scopeFilterByAlbumName($query, $albumName)
    {
        return $query->whereHas('album', function ($subquery) use ($albumName) {
            $subquery->where('name', 'LIKE', '%' . $albumName . '%');
        });
    }


    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }
    // Define the relationship with performers
    public function performers()
    {
        return $this->belongsToMany(Performer::class, 'artist_id');
    }
}
