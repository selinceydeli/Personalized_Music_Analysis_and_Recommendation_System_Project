<?php

namespace App\Http\Controllers;

use App\Http\Resources\AlbumRatingResource;
use Illuminate\Http\Request;
use App\Models\SongRating;
use App\Http\Resources\SongRatingResource;
use App\Models\AlbumRating;
use App\Models\PerformerRating;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SongRatingController extends Controller
{
    public function index()
    {
        $songratings = songRating::all();
        return response()->json($songratings);
    }

    public function store(Request $request)
    {
        $songrating = new SongRating;
        $songrating->rating = $request->input('rating');
        $songrating->username = auth()->user()->username;
        $songrating->song_id = $request->input('song_id');
        $songrating->date_rated = now();
        $songrating->save();

        return back()->with('message', 'Song rated successfully');
    }

    public function search_id_song($id)
    {

        $songratings = SongRating::where('song_id', '=', "{$id}")->get();

        return SongRatingResource::collection($songratings);
    }

    public function search_id_user($username)
    {

        $songratings = SongRating::where('username', '=', "{$username}")->get();

        return SongRatingResource::collection($songratings);
    }
    public function getLatestUserRating($username, $songId)
    {
        return SongRating::where('username', $username)
            ->where('song_id', $songId)
            ->orderByDesc('date_rated')
            ->first();
    }
    public function getLatestUserRatingForAlbum($username, $albumId)
    {
        return AlbumRating::where('username', $username)
            ->where('album_id', $albumId)
            ->orderByDesc('date_rated')
            ->first();
    }
    public function getLatestUserRatingForPerformer($username, $artist_id)
    {
        return PerformerRating::where('username', $username)
            ->where('artist_id', $artist_id)
            ->orderByDesc('date_rated')
            ->first();
    }



    public function update(Request $request, $id)
    {
        if (SongRating::where('id', $id)->exists()) {
            $songrating = SongRating::find($id);
            $songrating->rating = is_null($request->rating) ? $songrating->rating : $request->rating;
            $songrating->username = is_null($request->username) ? $songrating->username : $request->username;
            $songrating->song_id = is_null($request->song_id) ? $songrating->song_id : $request->song_id;
            $songrating->date_rated = is_null($request->date_rated) ? $songrating->date_rated : $request->date_rated;
            $songrating->save();
            return response()->json([
                "message" => "Song rating Updated"
            ], 200);
        } else {
            return response()->json([
                "message" => "Song rating not found!"
            ], 404);
        }
    }

    public function destroy($id)
    {
        if (SongRating::where('id', $id)->exists()) {
            $songrating = SongRating::find($id);
            $songrating->delete();
            return response()->json([
                "message" => "Song rating deleted"
            ], 200);
        } else {
            return response()->json([
                "message" => "Song rating not found"
            ], 404);
        }
    }

    // Methods defined for analysis functionality
    public function favorite10RatingsInGivenMonths($username, int $months)
    {
        // Calculate the date $months ago from today
        $sixMonthsAgo = now()->subMonths($months);

        // Create a subquery to get the top 10 rated, unique song IDs
        $subQuery = SongRating::select('song_id', DB::raw('AVG(rating) as average_rating'))
            ->where('username', $username)
            ->where('date_rated', '>=', $sixMonthsAgo)
            ->groupBy('song_id')
            ->orderBy('average_rating', 'DESC')
            ->take(10);

        // Now, perform a join to get the song information based on the top song IDs
        $topSongs = DB::table('songs')
            ->joinSub($subQuery, 'top_songs', function ($join) {
                $join->on('songs.song_id', '=', 'top_songs.song_id');
            })
            ->get([
                'songs.*', // Select all fields from songs
                // Here you can also join other song details if needed
                'top_songs.average_rating', // Get the average rating as well
            ]);

        return response()->json($topSongs);
    }

    public function getMonthlyAverageRatings($username)
    {
        $oneMonthAgo = Carbon::now()->subMonth();

        $dailyAverages = SongRating::select(
            DB::raw('DATE(date_rated) as date'),
            DB::raw('AVG(rating) as average_rating')
        )
            ->where('username', $username)
            ->where('date_rated', '>=', $oneMonthAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['date'] => $item['average_rating']];
            });

        return response()->json($dailyAverages);
    }
}
