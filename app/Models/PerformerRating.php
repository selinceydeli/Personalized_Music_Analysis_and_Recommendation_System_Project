<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformerRating extends Model
{
    use HasFactory;

    // This method defines a relationship to the Performer model
    public function performer()
    {
        return $this->belongsTo(Performer::class, 'artist_id', 'artist_id');
    }
}
