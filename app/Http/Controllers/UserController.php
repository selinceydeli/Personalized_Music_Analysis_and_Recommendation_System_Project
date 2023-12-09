<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\User;
use App\Models\Performer;
use App\Models\SongRating;
use Illuminate\Http\Request;
use App\Models\PerformerRating;
use App\Rules\SpecialCharacter;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SongResource;
use App\Http\Resources\UserResource;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\SongController;
use ReCaptcha\ReCaptcha; // Import the ReCaptcha class at the top

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Show Register/Create Form
    public function create()
    {
        return view('users.register');
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:255', Rule::unique('users', 'username')],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'min:6', new SpecialCharacter],
        ]);

        $response = (new ReCaptcha(env('RECAPTCHA_SECRET_KEY')))->verify($request->input('g-recaptcha-response'));

        if ($response->isSuccess()) {
            $formFields['password'] = bcrypt($formFields['password']);

            // Assign default values
            $formFields['language'] = 'English'; // Default language
            $formFields['subscription'] = 'free'; // Default subscription
            $formFields['rate_limit'] = '100'; // Default rate limit

            $user = User::create($formFields);

            auth()->login($user);

            return redirect('/')->with('message', 'User created and logged in');
        } else {
            return redirect('/register')->with('message', 'reCAPTCHA validation failed');
        }
    }


    // Instead of search_id() method, a search_username() method is defined
    // since the primary key of the Users table is username
    public function search_username($username)
    {
        $user = User::where('username', $username)->first();
        if ($user) {
            return response()->json($user);
        } else {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
    }
    //Methods for Recommendation
    public function favGenreRecomendationFromDifferentPerformers($username)
    {
        // Retrieve user's top-rated performers
        $topPerformersGenres = PerformerRating::where('username', $username)
            ->orderBy('rating', 'desc')
            ->with('performer')
            ->get()
            ->pluck('performer.genre')
            ->map(function ($genres) {
                return json_decode($genres);
            })
            ->flatten()
            ->unique()
            ->values()
            ->all();

        $similarPerformers = Performer::where(function ($query) use ($topPerformersGenres) {
            foreach ($topPerformersGenres as $genre) {
                $query->orWhereJsonContains('genre', $genre);
            }
        })->get()
            ->pluck('artist_id')
            ->toArray();

        // Retrieve the user's rated song IDs
        $ratedSongIds = SongRating::where('username', $username)->pluck('song_id')->toArray();

        // Retrieve the latest user ratings for songs
        $ratingsMap = [];
        $songRatingsController = new SongRatingController();
        foreach ($ratedSongIds as $songId) {
            $latestUserRating = $songRatingsController->getLatestUserRating($username, $songId);
            $ratingsMap[$songId] = [
                'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
            ];
        }

        // Retrieve unrated songs from similar performers
        $recommendedSongs = Song::where(function ($query) use ($similarPerformers) {
            foreach ($similarPerformers as $artistId) {
                $query->orWhereJsonContains('performers', (string)$artistId);
            }
        })
            ->whereNotIn('song_id', $ratedSongIds)
            ->with('ratings')
            ->get()
            ->filter(function ($song) use ($ratingsMap) {
                // Filter out songs that the user has already rated
                return !isset($ratingsMap[$song->song_id]);
            })
            ->sortByDesc('average_rating')
            ->take(15)
            ->values();

        return $recommendedSongs;
    }


    public function RecomendationByEnergyAndDanceability($username)
    {
        // Get top 20 rated songs by the user
        $topRatedSongs = DB::table('song_ratings')
            ->where('username', $username)
            ->orderBy('rating', 'desc')
            ->take(20)
            ->pluck('song_id');

        // Calculate average danceability and energy values
        $averages = Song::whereIn('song_id', $topRatedSongs)
            ->selectRaw('AVG(danceability) as average_danceability, AVG(energy) as average_energy')
            ->first();

        // Get 30 songs with closest danceability and energy values
        // and exclude songs already rated by the user
        $recommendedSongs = Song::whereNotIn('song_id', function ($query) use ($username) {
            $query->select('song_id')
                ->from('song_ratings')
                ->where('username', $username);
        })
            ->orderByRaw('ABS(danceability - ?) + ABS(energy - ?) ASC', [$averages->average_danceability, $averages->average_energy])
            ->take(15)
            ->get();
        foreach ($recommendedSongs as $song) {
            $ratings = SongRating::where('song_id', $song->song_id)->pluck('rating')->toArray();
            $averageRating = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : 0;

            // Assign the average rating to the song object
            $song->average_rating = $averageRating;
        }
        $recommendedSongs = $recommendedSongs->sortByDesc('average_rating')->values();

        $ratedSongIds = SongRating::where('username', $username)->pluck('song_id')->toArray();
        // Retrieve the latest user ratings for songs
        $ratingsMap = [];
        $songRatingsController = new SongRatingController();
        foreach ($ratedSongIds as $songId) {
            $latestUserRating = $songRatingsController->getLatestUserRating($username, $songId);
            $ratingsMap[$songId] = [
                'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
            ];
        }
        $recommendedSongs = $recommendedSongs->filter(function ($song) use ($ratingsMap) {
            // Filter out songs that have a null rating in the ratingsMap
            return !isset($ratingsMap[$song->song_id]['latest_user_rating']);
        });

        return $recommendedSongs;
    }

    public function dashboard()
    {
        $user = auth()->user();
        $notifications = $user->notifications;  // Get all notifications
        $unreadNotifications = $user->unreadNotifications;  // Get only unread notifications

        return view('dashboard', compact('notifications', 'unreadNotifications'));
    }

    //Logout User

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'You have been logged out!');
    }

    // Show Login Form

    public function login()
    {
        return view('users.login');
    }

    // Authenticate User

    public function authenticate(Request $request)
    {
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        //$response = (new ReCaptcha(env('RECAPTCHA_SECRET_KEY')))->verify($request->input('g-recaptcha-response'));

        if (auth()->attempt($formFields)) {

            // Get the authenticated user
            $user = auth()->user();

            // Retrieve the user's name from the database

            $request->session()->regenerate();

            // You can pass the user's name to the view or store it in the session
            //$request->session()->put('user_name', $userName);

            return redirect('/')->with('message', 'You are now logged in!');
        }

        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }
    public function getFriends($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return response()->json($user->allFriends);
    }

    public function getBlockedUsers($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return response()->json($user->blockedUsers);
    }
    public function getNotifications($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        // Ensure that the authenticated user is the same as the requested user
        if (auth()->user()->username != $username) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notifications = $user->notifications; // or use ->unreadNotifications for only unread ones
        return response()->json($notifications);
    }

    public function showDashboard()
    {
        $username = auth()->user()->username;

        $title = "";

        $ratingsMap = [];


        if (PerformerRating::where('username', '=', "{$username}")->exists()) {
            $recommendations = $this->favGenreRecomendationFromDifferentPerformers($username) ?? [];
            $title = "Top Picks From Your Favorite Genres";
            // Extract the song_id values from the paginated songs
            $songIds = $recommendations->pluck('song_id')->toArray();
            // Initialize an empty array to store the mapping

            if (auth()->check()) {
                $username = auth()->user()->username;

                // Iterate through each song ID and retrieve the latest user rating
                $songRatingsController = new SongRatingController();
                foreach ($songIds as $songId) {
                    // Retrieve the latest user rating for the current song
                    $latestUserRating = $songRatingsController->getLatestUserRating($username, $songId);

                    // Build the ratings map entry for this song
                    $ratingsMap[$songId] = [
                        'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
                    ];
                }
            }
        } else {
            $recommendations = $this->bestSongs() ?? [];
            $title = "Top Picks From Best Rated Songs";
        }

        $performerIds = $recommendations->pluck('performers')->flatten(); // Get unique performer IDs from all songs

        // Remove the extra brackets and extract the IDs as strings
        foreach ($performerIds as $key => $ids) {
            if (is_string($ids) && strpos($ids, ',') !== false) {
                $performerIds[$key] = array_map('trim', explode(',', trim($ids, '[]')));
            } else {
                $performerIds[$key] = trim($ids, '[]');
            }
        }

        $performerController = new PerformerController();
        $performers = [];
        foreach ($performerIds as $key => $id) {
            if (is_array($id)) {
                foreach ($id as $subId) {
                    // Make sure $subId is a string without quotes
                    $subId = trim($subId, '"');

                    // Make an HTTP request to fetch performer data for each subId
                    $response = $performerController->search_id($subId); // Assuming search_id() takes a string parameter

                    if ($response->getStatusCode() == 200) { // Checking if performer is found
                        $performers[$key][$subId] = $response->getData(); // Assuming getData() gets the data from the response
                    }
                }
            } else {
                // Make an HTTP request to fetch performer data for $id
                $id = trim($id, '"');

                $response = $performerController->search_id($id); // Assuming search_id() takes a string parameter

                if ($response->getStatusCode() == 200) { // Checking if performer is found
                    $performers[$key][$id] = $response->getData(); // Assuming getData() gets the data from the response
                }
            }
        }

        return view('components.dashboard', [
            'recommendations' => $recommendations,
            'title' => $title,
            'ratingsMap' => $ratingsMap,
            'performers' => $performers,
        ]);
    }

    public function showDashboardEnergy()
    {
        $username = auth()->user()->username;

        $ratingsMap = [];


        if (SongRating::where('username', '=', "{$username}")->exists()) {
            $recommendations = $this->RecomendationByEnergyAndDanceability($username) ?? [];
            $title = "Dynamic Beats: Tailored Picks Matching Your Energy & Dance Vibes";
            $songIds = $recommendations->pluck('song_id')->toArray();
            // Initialize an empty array to store the mapping

            if (auth()->check()) {
                $username = auth()->user()->username;

                // Iterate through each song ID and retrieve the latest user rating
                $songRatingsController = new SongRatingController();
                foreach ($songIds as $songId) {
                    // Retrieve the latest user rating for the current song
                    $latestUserRating = $songRatingsController->getLatestUserRating($username, $songId);

                    // Build the ratings map entry for this song
                    $ratingsMap[$songId] = [
                        'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
                    ];
                }
            }
        } else {
            $recommendations = $this->bestSongs() ?? [];
            $title = "Top Picks From Best Rated Songs";
        }

        $performerIds = $recommendations->pluck('performers')->flatten(); // Get unique performer IDs from all songs

        // Remove the extra brackets and extract the IDs as strings
        foreach ($performerIds as $key => $ids) {
            if (is_string($ids) && strpos($ids, ',') !== false) {
                $performerIds[$key] = array_map('trim', explode(',', trim($ids, '[]')));
            } else {
                $performerIds[$key] = trim($ids, '[]');
            }
        }

        $performerController = new PerformerController();
        $performers = [];
        foreach ($performerIds as $key => $id) {
            if (is_array($id)) {
                foreach ($id as $subId) {
                    // Make sure $subId is a string without quotes
                    $subId = trim($subId, '"');

                    // Make an HTTP request to fetch performer data for each subId
                    $response = $performerController->search_id($subId); // Assuming search_id() takes a string parameter

                    if ($response->getStatusCode() == 200) { // Checking if performer is found
                        $performers[$key][$subId] = $response->getData(); // Assuming getData() gets the data from the response
                    }
                }
            } else {
                // Make an HTTP request to fetch performer data for $id
                $id = trim($id, '"');

                $response = $performerController->search_id($id); // Assuming search_id() takes a string parameter

                if ($response->getStatusCode() == 200) { // Checking if performer is found
                    $performers[$key][$id] = $response->getData(); // Assuming getData() gets the data from the response
                }
            }
        }

        return view('components.dashboard-energy', [
            'recommendations' => $recommendations,
            'title' => $title,
            'ratingsMap' => $ratingsMap,
            'performers' => $performers,
        ]);
    }

    public function mobileauthenticate(Request $request)
    {
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        if (auth()->attempt($formFields)) {

            return true;
        }

        return false;
    }

    public function bestSongs()
    {
        $topRatedSongs = Song::join('song_ratings', 'songs.song_id', '=', 'song_ratings.song_id')  // Adjust the key names based on your table structure
            ->select('songs.*', DB::raw('AVG(song_ratings.rating) as average_rating'))
            ->groupBy('songs.song_id')  // Group by song id to calculate average
            ->orderBy('average_rating', 'desc')  // Order by the average rating
            ->take(20)  // Take top 20
            ->get();

        return $topRatedSongs;
    }
    //downloading recommendations (genre-based)
    public function downloadRecommendations()
    {
        $username = auth()->user()->username;

        // Instantiate UserController
        $userController = new UserController();

        // Fetch the recommendations using the method from UserController
        $recommendations = $userController->favGenreRecomendationFromDifferentPerformers($username);

        $jsonData = json_encode($recommendations, JSON_PRETTY_PRINT);
        $filename = "recommendations.json";

        return response($jsonData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename={$filename}"
        ]);
    }

    //downloading recommendations (energy-based)
    public function downloadRecommendationsEnergy()
    {
        $username = auth()->user()->username;

        // Instantiate UserController
        $userController = new UserController();

        // Fetch the recommendations using the method from UserController
        $recommendations = $userController->RecomendationByEnergyAndDanceability($username);

        $jsonData = json_encode($recommendations, JSON_PRETTY_PRINT);
        $filename = "recommendations.json";

        return response($jsonData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename={$filename}"
        ]);
    }
}
