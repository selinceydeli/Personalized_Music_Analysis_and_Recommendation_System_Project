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
}
