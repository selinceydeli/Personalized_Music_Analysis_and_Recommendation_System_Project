<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Http\Resources\SongResource;
use App\Models\Album;
use App\Http\Resources\AlbumResource;
use App\Models\Performer;
use App\Http\Resources\PerformerResource;
use App\Models\SongRating;
use App\Http\Resources\SongRatingResource;
use App\Models\AlbumRating;
use App\Http\Resources\AlbumRatingResource;
use App\Models\PerformerRating;
use App\Http\Resources\PerformerRatingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search_all($searchTerm){

        $songs = Song::where('name', 'LIKE', "%{$searchTerm}%")
        ->withAvg('songRatings', 'rating') // assuming songRatings is the correct relationship
        ->selectRaw("CASE WHEN name LIKE '{$searchTerm}%' THEN 1 ELSE 2 END as priority")
        ->orderBy('priority') // Order by priority first
        ->orderBy('song_ratings_avg_rating', 'desc') // Then order by average rating
        ->limit(8) // Limit the results here
        ->get()
        ->map(function ($song) {
            $song->search_type = 'song';
            return $song;
        });

        // Search in Albums with priority
        $albums = Album::where('name', 'LIKE', "%{$searchTerm}%")
        ->withAvg('albumRatings', 'rating') // assuming songRatings is the correct relationship
        ->selectRaw("CASE WHEN name LIKE '{$searchTerm}%' THEN 1 ELSE 2 END as priority")
        ->orderBy('priority') // Order by priority first
        ->orderBy('album_ratings_avg_rating', 'desc') // Then order by average rating
        ->limit(3) // Limit the results here
        ->get()
        ->map(function ($album) {
            $album->search_type = 'album';
            return $album;
        });

        // Search in Performers with priority
        $performers = Performer::where('name', 'LIKE', "%{$searchTerm}%")
            ->withAvg('performerRatings', 'rating') // assuming songRatings is the correct relationship
            ->selectRaw("CASE WHEN name LIKE '{$searchTerm}%' THEN 1 ELSE 2 END as priority")
            ->orderBy('priority') // Order by priority first
            ->orderBy('performer_ratings_avg_rating', 'desc') // Then order by average rating
            ->limit(3) // Limit the results here
            ->get()
            ->map(function ($performer) {
                $performer->search_type = 'performer';
                return $performer;
            });

        // Concatenate Results
        $results = $songs->concat($albums)->concat($performers);

        // Sort by Priority and then by Rating
        $sortedResults = $results->sortBy([['priority', 'asc'],['average_rating', 'desc']]);

        // Return sorted results
        return $sortedResults;
    }
    public function search_album($searchTerm){

        // Search in Albums with priority
        $albums = Album::where('name', 'LIKE', "%{$searchTerm}%")
        ->withAvg('albumRatings', 'rating') // assuming songRatings is the correct relationship
        ->selectRaw("CASE WHEN name LIKE '{$searchTerm}%' THEN 1 ELSE 2 END as priority")
        ->orderBy('priority') // Order by priority first
        ->orderBy('album_ratings_avg_rating', 'desc') // Then order by average rating
        ->limit(30) // Limit the results here
        ->get()
        ->map(function ($album) {
            $album->search_type = 'album';
            return $album;
        });

        // Return sorted results
        return $albums;
    }
    public function search_song($searchTerm){

        // Search in Songs with priority, limit results in the query
        $songs = Song::where('name', 'LIKE', "%{$searchTerm}%")
        ->withAvg('songRatings', 'rating') // assuming songRatings is the correct relationship
        ->selectRaw("CASE WHEN name LIKE '{$searchTerm}%' THEN 1 ELSE 2 END as priority")
        ->orderBy('priority') // Order by priority first
        ->orderBy('song_ratings_avg_rating', 'desc') // Then order by average rating
        ->limit(30) // Limit the results here
        ->get()
        ->map(function ($song) {
            $song->search_type = 'song';
            return $song;
        });

        // Return sorted results
        return $songs;
    }
    public function search_performer($searchTerm){


        $performers = Performer::where('name', 'LIKE', "%{$searchTerm}%")
            ->withAvg('performerRatings', 'rating') // assuming songRatings is the correct relationship
            ->selectRaw("CASE WHEN name LIKE '{$searchTerm}%' THEN 1 ELSE 2 END as priority")
            ->orderBy('priority') // Order by priority first
            ->orderBy('performer_ratings_avg_rating', 'desc') // Then order by average rating
            ->limit(30) // Limit the results here
            ->get()
            ->map(function ($performer) {
                $performer->search_type = 'performer';
                return $performer;
            });

        // Return sorted results
        return $performers;
    }
}
