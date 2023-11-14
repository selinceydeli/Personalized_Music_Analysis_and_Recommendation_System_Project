<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    public function scopeFilter($query, array $filters) {
        if($filters['genre'] ?? false) {
            $requestedGenre = request('genre');
            $genres = explode('/', $requestedGenre);

            $query->where(function ($subquery) use ($genres) {
                // Check for an exact match
                foreach ($genres as $genre) {
                    $subquery->orWhere('genre', 'LIKE', '%' . $genre . '%');
                    $subquery->orWhere('genre', 'LIKE', $genre . '%');
                    $subquery->orWhere('genre', 'LIKE', '%' . $genre);
                    $subquery->orWhere('genre', 'LIKE', $genre);
                }
            });
        }
        
        if($filters['search'] ?? false) {
            $query->where('name', 'like', '%'. request('search'). '%')
            ->orwhere('genre', 'like', '%'. request('search'). '%');
            //album arama buradan
        }
    }

    public function album() {
        return $this->belongsTo(Album::class);
    }
    // Define the relationship with performers
    public function performers()
    {
        return $this->belongsToMany(Performer::class);
    }
    
}
