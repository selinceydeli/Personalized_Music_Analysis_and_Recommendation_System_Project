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

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'username';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username', 'email', 'name', 'surname', 'password', 'date_of_birth', 'language', 'subscription', 'rate_limit',
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
        'username' => 'string', // Cast username as a string
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
        return $this->hasMany(Block::class, 'blocker_id');
    }

    public function blockingUsers() {
        return $this->hasMany(Block::class, 'blocked_id');
    }

    public function getFriendsAttribute() {
        $friendsOfMine = $this->friendsOfMine ?: collect([]);
        $friendOf = $this->friendOf ?: collect([]);

        return $friendsOfMine->merge($friendOf);
    }
}
