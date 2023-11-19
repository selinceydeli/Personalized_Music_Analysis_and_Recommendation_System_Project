<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Performer;
use App\Models\Song;
use App\Http\Resources\SongResource;
use App\Http\Resources\UserResource;
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

    public function favGenreRecomendationFromDifferentPerformers($username){

        $topPerformers = DB::table('performer_ratings')
                            ->where('username', $username)
                            ->select('artist_id', DB::raw('AVG(rating) as average_rating'))
                            ->groupBy('artist_id')
                            ->orderBy('average_rating', 'desc')
                            ->take(5)
                            ->get();

        $genres = Performer::whereIn('id', $topPerformers->pluck('performer_id'))->pluck('genre');

        $averageRatingsSubquery = DB::table('song_ratings')
                                    ->select('song_id', DB::raw('AVG(rating) as average_rating'))
                                    ->groupBy('song_id');

        $excludePerformersSubquery = DB::table('songs')
                                        ->select('id')
                                        ->whereJsonDoesntContain('performers', $topPerformers->pluck('performer_id')->toArray());

        $genreMatchSubquery = DB::table('performers')
                                ->select('id')
                                ->whereIn('genre', $genres);

        $recommendedSongs = Song::whereIn('id', $genreMatchSubquery)
        ->whereIn('id', $excludePerformersSubquery)
        ->joinSub($averageRatingsSubquery, 'average_ratings', function ($join) {
            $join->on('songs.id', '=', 'average_ratings.song_id');
        })
        ->orderBy('average_ratings.average_rating', 'desc')
        ->take(20)
        ->get(['songs.*', 'average_ratings.average_rating']);


        return SongResource::collection($recommendedSongs);
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