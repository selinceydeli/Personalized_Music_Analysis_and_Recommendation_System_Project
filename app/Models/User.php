<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Playlist;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'username'; // Set the primary key to 'username'
    public $incrementing = false; // Indicate that the primary key is not auto-incrementing
    protected $keyType = 'string'; // Indicate the data type of the primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function friendsOfMine() {
        return $this->belongsToMany('App\Models\User', 'friendships', 'requester', 'user_requested')
                    ->wherePivot('status', '=', 1) // status 1 for accepted
                    ->withPivot('status');
    }

    public function friendOf() {
        return $this->belongsToMany('App\Models\User', 'friendships', 'user_requested', 'requester')
                    ->wherePivot('status', '=', 1)
                    ->withPivot('status');
    }

    public function blockedUsers() {
        return $this->hasMany(Block::class, 'blocker_username');
    }

    public function blockingUsers() {
        return $this->hasMany(Block::class, 'blocked_username');
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'user_playlist', 'username', 'playlist_id');
    }

    // Accessor to get all friends
    public function getFriendsAttribute() {
        $friendsOfMine = $this->friendsOfMine ?: collect([]);
        $friendOf = $this->friendOf ?: collect([]);

        return $friendsOfMine->merge($friendOf);
    }
}
