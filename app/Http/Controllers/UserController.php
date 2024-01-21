<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\SongResource;
use App\Models\Performer;
use App\Models\PerformerRating;
use App\Models\SongRating;
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

    public function update(Request $request, $username) {
        if (User::where('username', $username)->exists()) {
            $user = User::find($username);
            $user->email = $request->email ?? $user->email;
            $user->name = $request->name ?? $user->name;
            $user->surname = $request->surname ?? $user->surname;
            $user->password = $request->password ?? $user->password; // Make sure to hash the password if it's changed
            $user->date_of_birth = $request->date_of_birth ?? $user->date_of_birth;
            $user->language = $request->language ?? $user->language;
            $user->subscription = $request->subscription ?? $user->subscription;
            $user->rate_limit = $request->rate_limit ?? $user->rate_limit;
            $user->theme = $request->theme ?? $user->theme;
            $user->image = $request->image ?? $user->image;
            // Add other fields as necessary

            $user->save();
            return response()->json([
                "message" => "User Updated"
            ], 200);
        } else {
            return response()->json([
                "message" => "User not found!"
            ], 404);
        }
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
    public function getImg($username)
    {
        $user = User::where('username', $username)->first();
        if ($user) {
            if ($user->image == NULL){
                return response()->json([
                    "message" => "Image not found"
                ], 404);
            }
            $decodedImage = base64_decode($user->image);
            return response($decodedImage)->header('Content-Type', 'image/png');
        } else {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
    }
    public function uploadImg($username,Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if (User::where('username', $username)->exists()){
            $userObject = User::find($username);
            $file = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $userObject->image = $file;
            $userObject->save();
            return response()->json($userObject->image);
        }
        else {
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
        
        $ratedSongIds = SongRating::where('username', $username)->pluck('song_id')->toArray();

        // Retrieve top-rated songs from these performers
        $recommendedSongs = Song::where(function ($query) use ($similarPerformers) {
            foreach ($similarPerformers as $artistId) {
                // Adjust the query to check if the JSON array contains the artistId as a string
                $query->orWhereJsonContains('performers', (string)$artistId);
            }
        })
        ->whereNotIn('song_id', $ratedSongIds)
        ->with('songRatings') // Load the song ratings relationship
        ->get()
        ->sortByDesc('average_rating') // Sort by the accessor 'average_rating'
        ->take(20) // Limit to 20 songs for recommendation
        ->values();

        return $recommendedSongs;
    }

    public function favPositiveRecomendation($username){
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
        
        $ratedSongIds = SongRating::where('username', $username)->pluck('song_id')->toArray();

        // Retrieve top-rated songs from these performers
        $recommendedSongs = Song::where(function ($query) use ($similarPerformers) {
            foreach ($similarPerformers as $artistId) {
                // Adjust the query to check if the JSON array contains the artistId as a string
                $query->orWhereJsonContains('performers', (string)$artistId);
            }
        })
        ->whereNotIn('song_id', $ratedSongIds)
        ->where('valence', '>=', 0.75)
        ->where('valence', '<=', 1)
        ->with('songRatings') // Load the song ratings relationship
        ->get()
        ->sortByDesc('average_rating') // Sort by the accessor 'average_rating'
        ->take(20) // Limit to 20 songs for recommendation
        ->makeHidden(['ratings']); // Hides the ratings field

        return $recommendedSongs;
    }

    public function favNegativeRecomendation($username){
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
        
        $ratedSongIds = SongRating::where('username', $username)->pluck('song_id')->toArray();

        // Retrieve top-rated songs from these performers
        $recommendedSongs = Song::where(function ($query) use ($similarPerformers) {
            foreach ($similarPerformers as $artistId) {
                // Adjust the query to check if the JSON array contains the artistId as a string
                $query->orWhereJsonContains('performers', (string)$artistId);
            }
        })
        ->whereNotIn('song_id', $ratedSongIds)
        ->where('valence', '<=', 0.25)
        ->where('valence', '>=', 0)
        ->with('songRatings') // Load the song ratings relationship
        ->get()
        ->sortByDesc('average_rating') // Sort by the accessor 'average_rating'
        ->take(20) // Limit to 20 songs for recommendation
        ->makeHidden(['ratings']); // Hides the ratings field

        return $recommendedSongs;
    }
    

    public function RecomendationByEnergyAndDanceability($username) {
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
        
        $ratedSongIds = SongRating::where('username', $username)->pluck('song_id')->toArray();

        // Retrieve top-rated songs from these performers
        $recommendedSongs = Song::where(function ($query) use ($similarPerformers) {
            foreach ($similarPerformers as $artistId) {
                $query->orWhereJsonContains('performers', (string)$artistId);
            }
        })
        ->whereNotIn('song_id', $ratedSongIds)
        ->where('danceability', '>=', 0.75)
        ->where('energy', '>=', 0.75)
        ->get()
        ->sortByDesc(function($song) {
            return $song->average_rating;
        })
        ->take(20)
        ->makeHidden(['ratings']); // Hides the ratings field
    
        return $recommendedSongs;
    }


    public function dashboard()
        {
            $user = auth()->user();
            $notifications = $user->notifications;  // Get all notifications
            $unreadNotifications = $user->unreadNotifications;  // Get only unread notifications

            return view('dashboard', compact('notifications', 'unreadNotifications'));
        }

    public function getFriends($username)
        {
            $user = User::with(['friendsOfMine', 'friendOf'])->where('username', $username)->first();
            return response()->json($user->friends);
        }
        
    public function getBlockedUsers($username)
        {
            $user = User::where('username', $username)->firstOrFail();
            return response()->json($user->blockedUsers);
        }


    public function mobileauthenticate(Request $request) {
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        if (auth()->attempt($formFields)) {
    
            return true;
        }
    
        return false;
    }

    public function bestSongs(){
        $topRatedSongs = DB::table('song_ratings')
                ->orderBy('rating', 'desc')
                ->take(20)
                ->pluck('song_id');

        return SongResource::collection($topRatedSongs);
    }


    public function testFriendships($username) {
        $user = User::with(['friendsOfMine', 'friendOf'])->where('username', $username)->first();
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Test friendsOfMine
        $friendsOfMine = $user->friendsOfMine;
        // Test friendOf
        $friendOf = $user->friendOf;
    
        // Return the results
        return response()->json([
            'friendsOfMine' => $friendsOfMine,
            'friendOf' => $friendOf,
        ]);
    }
}























