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

        // Search in Songs
        $songs = Song::where('name', 'LIKE', "%{$searchTerm}%")
                    ->with('songRatings') // assuming you have a relationship set up to fetch ratings
                    ->get()
                    ->map(function ($song) {
                        $song->search_type = 'song';
                        $song->rating = $song->songRatings->avg('rating'); // calculate average rating
                        return $song;
                    });

        // Search in Albums
        $albums = Album::where('name', 'LIKE', "%{$searchTerm}%")
                    ->with('albumRatings') // assuming you have a relationship set up to fetch ratings
                    ->get()
                    ->map(function ($album) {
                        $album->search_type = 'album';
                        $album->rating = $album->albumRatings->avg('rating'); // calculate average rating
                        return $album;
                    });

        // Search in Performers
        $performers = Performer::where('name', 'LIKE', "%{$searchTerm}%")
                            ->with('performerRatings') // assuming you have a relationship set up to fetch ratings
                            ->get()
                            ->map(function ($performer) {
                                $performer->search_type = 'performer';
                                $performer->rating = $performer->performerRatings->avg('rating'); // calculate average rating
                                return $performer;
                            });

        // Aggregate Results
        $results = $songs->merge($albums)->merge($performers);

        // Sort by Rating
        $sortedResults = $results->sortByDesc('rating');

        // Return sorted results
        return $sortedResults;
    }
    public function search_song($searchTerm){

        // Search in Songs
        $songs = Song::where('name', 'LIKE', "%{$searchTerm}%")
                    ->with('songRatings') // assuming you have a relationship set up to fetch ratings
                    ->get()
                    ->map(function ($song) {
                        $song->search_type = 'song';
                        $song->rating = $song->songRatings->avg('rating'); // calculate average rating
                        return $song;
                    });


        // Sort by Rating
        $sortedResults = $songs->sortByDesc('rating');

        // Return sorted results
        return $sortedResults;
    }
    public function search_album($searchTerm){

        $albums = Album::where('name', 'LIKE', "%{$searchTerm}%")
                    ->with('albumRatings') // assuming you have a relationship set up to fetch ratings
                    ->get()
                    ->map(function ($album) {
                        $album->search_type = 'album';
                        $album->rating = $album->albumRatings->avg('rating'); // calculate average rating
                        return $album;
                    });


        // Sort by Rating
        $sortedResults = $albums->sortByDesc('rating');

        // Return sorted results
        return $sortedResults;
    }
    public function search_performer($searchTerm){


        $performers = Performer::where('name', 'LIKE', "%{$searchTerm}%")
                            ->with('performerRatings') // assuming you have a relationship set up to fetch ratings
                            ->get()
                            ->map(function ($performer) {
                                $performer->search_type = 'performer';
                                $performer->rating = $performer->performerRatings->avg('rating'); // calculate average rating
                                return $performer;
                            });


        // Sort by Rating
        $sortedResults = $performers->sortByDesc('rating');

        // Return sorted results
        return $sortedResults;
    }
}
