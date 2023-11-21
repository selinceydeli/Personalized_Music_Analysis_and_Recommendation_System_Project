<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Album extends Model
{
    use HasFactory;

    protected $primaryKey = 'album_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($album) {
            $album->{$album->getKeyName()} = (string) Str::uuid();
        });

    }

    public function albumRatings()
    {
        return $this->hasMany(albumRating::class, 'album_id', 'album_id');
    }
}
