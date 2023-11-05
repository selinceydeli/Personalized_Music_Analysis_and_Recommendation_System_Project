<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformerRating extends Model
{
    use HasFactory;

    protected $table = 'performer_ratings';

    protected $fillable = [
        'rating',
        'username',
        'perf_id',
        'date_rated',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    public function performer()
    {
        return $this->belongsTo(Performer::class, 'perf_id', 'id');
    }

    protected $casts = [
        'date_rated' => 'date',
        'rating' => 'decimal:1',
    ];
}
