<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlbumRating extends Model
{
    use HasFactory;

    protected $table = 'album_ratings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'rating',
        'username',
        'album_id',
        'date_rated',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'decimal:1',
        'date_rated' => 'date',
    ];

    /**
     * Get the user that created the rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    /**
     * Get the album that the rating refers to.
     */
    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
