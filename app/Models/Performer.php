<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Performer extends Model
{
    use HasFactory;

    protected $table = 'performers';

    protected $fillable = [
        'name',
        'nationality',
    ];

    public function ratings()
    {
        return $this->hasMany(PerformerRating::class, 'perf_id', 'id');
    }
}