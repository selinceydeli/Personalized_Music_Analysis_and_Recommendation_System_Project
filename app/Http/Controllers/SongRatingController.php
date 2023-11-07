<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SongRating;
use App\Http\Resources\SongRatingResource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Carbon is a date manipulation library for PHP

class SongRatingController extends Controller
{
    public function index(){
        $songratings = songRating::all();
        return response()->json($songratings);
    }

    public function store(Request $request){
        $songrating = new SongRating;
        $songrating->rating = $request->rating;
        $songrating->username = $request->username;
        $songrating->song_id = $request->song_id;
        $songrating->date_rated = $request->date_rated;
        $songrating->save();
        return response()->json([
            "message" => "Song rating added"
        ], 200);
    }

    public function search_id_song($id){

        $songratings = SongRating::where('song_id', '=', "{$id}")->get();

        return SongRatingResource::collection($songratings);
    }

    public function search_id_user($username){

        $songratings = SongRating::where('username', '=', "{$username}")->get();

        return SongRatingResource::collection($songratings);
    }

    public function update(Request $request, $id){
        if (SongRating::where('id', $id) -> exists()){
            $songrating = SongRating::find($id);
            $songrating->rating = is_null($request -> rating) ? $songrating->rating : $request->rating;
            $songrating->username = is_null($request -> username) ? $songrating->username : $request->username;
            $songrating->song_id = is_null($request -> song_id) ? $songrating->song_id : $request->song_id;
            $songrating->date_rated = is_null($request -> date_rated) ? $songrating->date_rated : $request->date_rated;
            $songrating->save();
            return response()->json([
                "message" => "Song rating Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Song rating not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (SongRating::where('id', $id) -> exists()){
            $songrating = SongRating::find($id);
            $songrating->delete();
            return response()->json([
                "message" => "Song rating deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Song rating not found"
            ], 404);
        }
    }

    // Methods defined for analysis functionality
    public function favorite10RatingsIn6Months($username)
    {
        // Calculate the date 6 months ago from today
        $sixMonthsAgo = now()->subMonths(6);

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
                            $join->on('songs.id', '=', 'top_songs.song_id');
                        })
                        ->get([
                            'songs.*', // Select all fields from songs
                            // Here you can also join other song details if needed
                            'top_songs.average_rating', // Get the average rating as well
                        ]);

        return response()->json($topSongs);
    }

    public function averageRatingByPerformer($username, $performerName)
    {
        // Calculate the date 10 months ago from now
        $tenMonthsAgo = Carbon::now()->subMonths(10)->startOfDay();

        // First, get the IDs of performers with the given name
        $performerIds = DB::table('performers')
            ->where('name', $performerName)
            ->get()
            ->pluck('id'); // Retrieve only the IDs

        // Check if performers with the given name exist
        if ($performerIds->isEmpty()) {
            return response()->json(['message' => 'Performer not found.'], 404);
        }

        // Create a subquery to get all song IDs by the given performer using their ID
        $songsByPerformerSubQuery = DB::table('songs')
            ->select('id')
            ->where(function ($query) use ($performerIds) {
                foreach ($performerIds as $performerId) {
                    $query->orWhereJsonContains('performers', $performerId);
                }
            });

        // Now, join the song ratings with the subquery of song IDs by the performer
        // and calculate the average rating for these songs rated by the given user
        // within the last 10 months
        $averageRating = DB::table('song_ratings')
            ->joinSub($songsByPerformerSubQuery, 'performer_songs', function ($join) {
                $join->on('song_ratings.song_id', '=', 'performer_songs.id');
            })
            ->where('username', $username)
            ->where('date_rated', '>=', $tenMonthsAgo)
            ->select(DB::raw('AVG(rating) as average_rating'))
            ->first(); // Since we're interested in the average of all, we can limit to first

        // Check if we have a result and return appropriately
        if ($averageRating && $averageRating->average_rating) {
            return response()->json($averageRating);
        } else {
            return response()->json(['message' => 'No ratings found for the specified performer and user within the last 10 months.'], 404);
        }
    }

   
}