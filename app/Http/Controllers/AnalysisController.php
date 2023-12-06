<?php

// app/Http/Controllers/AnalysisController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AlbumRatingController;
use App\Http\Controllers\SongRatingController;
use App\Http\Controllers\PerformerRatingController;

class AnalysisController extends Controller
{
    public function favoriteAlbums(Request $request, $username, $era)
    {
        // Fetch the top rated albums data
        $topAlbums = $this->topRatedAlbumsByEra($username, $era);

        // Get eras (you may retrieve this data from a model or another source)
        $eras = ['50s', '60s', '70s', '80s', '90s', '20s'];

        return view('analysis.favorite_albums', compact('topAlbums', 'eras'));
    }

    public function favoriteSongs(Request $request, SongRatingController $songRatingController)
    {
        $months = $songRatingController->getDistinctMonths();

        return view('analysis.favorite_songs', ['months' => $months]);
    }

    public function averageRatings(Request $request, PerformerRatingController $performerRatingController)
    {
        $artists = $performerRatingController->getDistinctArtists();

        // You may also need to fetch time spans based on your requirements

        return view('analysis.average_ratings', ['artists' => $artists]);
    }

    public function dailyAverage(Request $request)
    {
        // Fetch necessary data, including time-related information

        return view('analysis.daily_average');
    }
}
