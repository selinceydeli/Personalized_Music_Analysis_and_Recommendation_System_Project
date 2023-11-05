<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $table = 'albums';

    protected $fillable = [
        'name',
        'is_single',
        'image_url',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function ratings()
    {
        return $this->hasMany(AlbumRating::class, 'album_id', 'id');
    }

    public function songs()
    {
        return $this->hasMany(Song::class, 'album_id', 'id');
    }

}
