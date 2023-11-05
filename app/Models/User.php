<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string, string>
     */
    protected $fillable = [
        'username',
        'email',
        'name',
        'surname',
        'password',
        'date_of_birth',
        'language',
        'subscription',
        'rate_limit',
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'username';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, mixed>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // Removed 'password' => 'hashed', because Laravel automatically hashes passwords unless this behavior is customized.
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'language' => 'en', // Assuming 'en' is the default language
        'subscription' => 'free', // Assuming 'free' is the default subscription type
        'rate_limit' => 'standard', // Assuming 'standard' is the default rate limit
    ];

    public function albumRatings()
    {
        return $this->hasMany(AlbumRating::class, 'username', 'username');
    }

    public function performerRatings()
    {
        return $this->hasMany(PerformerRating::class, 'username', 'username');
    }

    public function songRatings()
    {
        return $this->hasMany(SongRating::class, 'username', 'username');
    }

}