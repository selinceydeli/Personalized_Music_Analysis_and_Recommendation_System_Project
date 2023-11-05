<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformerRating extends Model
{
    use HasFactory;

    // The name of the table associated with the model
    protected $table = 'performer_ratings';

    // The attributes that are mass assignable
    protected $fillable = [
        'rating',
        'username',
        'perf_id',
        'date_rated',
    ];

    // Define relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    // Define relationship to Performer
    public function performer()
    {
        return $this->belongsTo(Performer::class, 'perf_id');
    }

    // Casts for proper data types and format
    protected $casts = [
        'date_rated' => 'date', // Ensure that date_rated is cast to a date
        'rating' => 'decimal:1', // Cast rating to a decimal with 1 place
    ];
}
