<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Performer extends Model
{
    use HasFactory;

    protected $primaryKey = 'artist_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($performer) {
            $performer->{$performer->getKeyName()} = (string) Str::uuid();
        });
    }

    public function performerRatings()
    {
        return $this->hasMany(PerformerRating::class, 'artist_id', 'artist_id');
    }
}
