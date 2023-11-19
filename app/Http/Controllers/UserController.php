<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\SongResource;
use App\Models\Performer;
use App\Models\PerformerRating;
use App\Models\Song;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request){
        $user = new User;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->email_verified_at = $request->email_verified_at;
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->password = $request->password;
        $user->date_of_birth = $request->date_of_birth;
        $user->language = $request->language;
        $user->subscription = $request->subscription;
        $user->rate_limit = $request->rate_limit;
        $user->save();
        return response()->json([
            "message" => "User added"
        ], 200);
    }

    // Instead of search_id() method, a search_username() method is defined
    // since the primary key of the Users table is username
    public function search_username($username){
        $user = User::where('username', $username)->first();
        if($user){
            return response()->json($user);
        } else {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
    }    

    //Methods for Recommendation
    public function favGenreRecomendationFromDifferentPerformers($username){
        // Retrieve user's top-rated performers
        $topPerformersGenres = PerformerRating::where('username', $username)
            ->orderBy('rating', 'desc')
            ->with('performer') // Assuming a relationship is defined in the PerformerRating model
            ->get()
            ->pluck('performer.genre')
            ->map(function ($genres) {
                return json_decode($genres); // Convert JSON string to PHP array
            })
            ->flatten()
            ->unique()
            ->values()
            ->all(); // $topPerformersGenres is an array of all unique genres that the top performers are associated with

        // Find performers with any of the genres from top performers
        $similarPerformers = Performer::where(function ($query) use ($topPerformersGenres) {
            foreach ($topPerformersGenres as $genre) {
                $query->orWhereJsonContains('genre', $genre); // Check if the performer's genres contain any of the top genres
            }
        })->get()
            ->pluck('artist_id')
            ->toArray();
            
        // Retrieve top-rated songs from these performers
        // Retrieve top-rated songs from these performers
        $recommendedSongs = Song::where(function ($query) use ($similarPerformers) {
            foreach ($similarPerformers as $artistId) {
                $query->orWhereJsonContains('performers', ['artist_id' => $artistId]);
            }
        })
        ->with('ratings') // Load the song ratings relationship
        ->get()
        ->sortByDesc('average_rating') // Sort by the accessor 'average_rating'
        ->take(20) // Limit to 20 songs for recommendation
        ->values();

        return $recommendedSongs;
    }

    public function RecomendationByEnergyAndDanceability($username){
        // Step 1: Get top 20 rated songs by the user
        $topRatedSongs = DB::table('song_ratings')
                ->where('username', $username)
                ->orderBy('rating', 'desc')
                ->take(20)
                ->pluck('song_id');

        // Step 2: Calculate average danceability and energy values
        $averages = Song::whereIn('song_id', $topRatedSongs)
                ->selectRaw('AVG(danceability) as average_danceability, AVG(energy) as average_energy')
                ->first();

        // Step 3: Get 30 songs with closest danceability and energy values
        // and exclude songs already rated by the user
        $recommendedSongs = Song::whereNotIn('song_id', function($query) use ($username) {
            $query->select('song_id')
                ->from('song_ratings')
                ->where('username', $username);
        })
        ->orderByRaw('ABS(danceability - ?) + ABS(energy - ?) ASC', [$averages->average_danceability, $averages->average_energy])
        ->take(30)
        ->get();

        return SongResource::collection($recommendedSongs);
    }
}