<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\User;
use App\Models\Block;
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
use App\Http\Controllers\FriendshipController;
use ReCaptcha\ReCaptcha; // Import the ReCaptcha class at the top
use App\Http\Controllers\AlbumRatingController;


class UserController extends Controller
{
    public function showProfile($username)
    {
        // Fetch the user based on the provided username
        $user = User::where('username', $username)->first();

        // Check if the user exists
        if (!$user) {
            // Redirect or show an error if the user doesn't exist
            return redirect()->back()->with('error', 'User not found.');
        }

        $blockedUsers = Block::where('blocker_username', $username)->pluck('blocked_username');

        // Check if the authenticated user has blocked $username
        if ($blockedUsers->contains(auth()->user()->username)) {
            return redirect()->back()->with('message', 'User not available');
        }


        // Retrieve additional data as required, e.g., user's playlists
        $playlists = $user->playlists; // Assuming a relationship with playlists
        $top5Albums = $this->top5Albums($username);
        $top5Songs = $this->top5Songs($username);
        $songOfYear = $this->songOfYear($username);
        $favGenres = $this->favGenres($username);
        //dd($favGenres);
        
        // Return the user profile view with the user data
        return view('users.user-profile', compact('user', 'playlists', 'top5Albums', 'top5Songs', 'songOfYear', 'favGenres'));
    }

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
        $command = "python3 tempFunctions/sendMail.py " . escapeshellarg($formFields["email"]) . " 2>&1";
        $result = shell_exec($command);
        if ($response->isSuccess()) {
            $formFields['password'] = bcrypt($formFields['password']);

            // Assign default values
            $formFields['language'] = 'English'; // Default language
            $formFields['subscription'] = 'free'; // Default subscription
            $formFields['rate_limit'] = '100'; // Default rate limit
            $formFields['theme'] = 'pink'; // Default rate limit

            $user = User::create($formFields);

            return redirect('/login')->with('message', 'User created');
        } else {
            return redirect('/register')->with('message', 'reCAPTCHA validation failed');
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

    public function favPositiveRecomendation($username)
    {
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
            ->where('valence', '<=', 1)
            ->where('valence', '>=', 0.75)
            ->with('ratings') // Load the song ratings relationship
            ->get()
            ->sortByDesc('average_rating') // Sort by the accessor 'average_rating'
            ->take(15); // Limit to 20 songs for recommendation

        return $recommendedSongs;
    }

    public function showDashboardNegative()
    {
        $username = auth()->user()->username;
        $recommendations = $this->favNegativeRecomendation($username) ?? [];


        $ratingsMap = [];

        $songIds = $recommendations->pluck('song_id')->toArray();

        $songRatingsController = new SongRatingController();
        foreach ($songIds as $songId) {
            // Retrieve the latest user rating for the current song
            $latestUserRating = $songRatingsController->getLatestUserRating($username, $songId);

            // Build the ratings map entry for this song
            $ratingsMap[$songId] = [
                'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
            ];
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
        return view('components.dashboard-negative', ['recommendations' => $recommendations, 'performers' => $performers, 'ratingsMap' => $ratingsMap]);
    }

    public function showDashboardPositive()
    {
        $username = auth()->user()->username;
        $recommendations = $this->favPositiveRecomendation($username) ?? [];


        $ratingsMap = [];

        $songIds = $recommendations->pluck('song_id')->toArray();

        $songRatingsController = new SongRatingController();
        foreach ($songIds as $songId) {
            // Retrieve the latest user rating for the current song
            $latestUserRating = $songRatingsController->getLatestUserRating($username, $songId);

            // Build the ratings map entry for this song
            $ratingsMap[$songId] = [
                'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
            ];
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
        return view('components.dashboard-positive', ['recommendations' => $recommendations, 'performers' => $performers, 'ratingsMap' => $ratingsMap]);
    }

    public function favNegativeRecomendation($username)
    {
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
            ->with('ratings') // Load the song ratings relationship
            ->get()
            ->sortByDesc('average_rating') // Sort by the accessor 'average_rating'
            ->take(15); // Limit to 20 songs for recommendation

        return $recommendedSongs;
    }

    public function RecomendationByEnergyAndDanceability($username)
    {
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
            ->where('energy', '>=', 0.75)
            ->where('danceability', '>=', 0.75)
            ->with('ratings') // Load the song ratings relationship
            ->get()
            ->sortByDesc('average_rating') // Sort by the accessor 'average_rating'
            ->take(15); // Limit to 20 songs for recommendation

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
        $user = User::with(['friendsOfMine', 'friendOf'])
            ->where('username', $username)
            ->withCount('friendsOfMine as totalFriends')
            ->orderByDesc('totalFriends')
            ->first();

        return $user->friends;
    }
    public function getNonFriends($username)
    {
        $user = User::with('friendsOfMine')->where('username', $username)->first();
        $blockedUsers = $this->getBlockedUsers($username)->pluck('blocked_username');

        $nonFriends = User::where('username', '!=', $user->username)
            ->whereDoesntHave('friendsOfMine', function ($query) use ($user) {
                $query->where('username', '=', $user->username);
            })
            ->whereNotIn('username', $blockedUsers) // Exclude blocked users
            ->withCount('friendsOfMine')
            ->orderByDesc('friends_of_mine_count');

        return $nonFriends;
    }

    public function getBlockedUsers($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return $user->blockedUsers;
    }
    public function addfriends()
    {
        $friendshipController = new FriendshipController();
        $username = auth()->user()->username;
        $nonFriends = $friendshipController->getAllFriends($username);
        $searchTerm = request('searchuser');
        if ($searchTerm) {
            $nonFriends->where(function ($query) use ($searchTerm) {
                $query->where('username', 'like', '%' . $searchTerm . '%');
            });
        }
        $nonFriends = $nonFriends->paginate(10);
        $pending = $friendshipController->getPendingFriendRequests($username);
        return view(
            'friend.index',
            [
                'nonFriends' => $nonFriends,
                'pending' => $pending,
            ]
        );
    }

    public function requests()
    {
        $friendshipController = new FriendshipController();
        $requests = $friendshipController->seeRequests();
        $searchTerm = request('searchrequest');
        if ($searchTerm) {
            $requests->where(function ($query) use ($searchTerm) {
                $query->where('username', 'like', '%' . $searchTerm . '%');
            });
        }
        $requests = $requests->paginate(10);

        return view(
            'friend.requests',
            [
                'requests' => $requests,
            ]
        );
    }

    public function myfriends()
    {
        $username = auth()->user()->username;
        $friendshipController = new FriendshipController();
        $allFriends = $friendshipController->getFriends($username);
        $searchTerm = request('searchfriend');
        if ($searchTerm) {
            $allFriends->where(function ($query) use ($searchTerm) {
                $query->where('username', 'like', '%' . $searchTerm . '%');
            });
        }
        $allFriends = $allFriends->paginate(10);

        return view(
            'friend.show',
            [
                'allFriends' => $allFriends,
            ]
        );
    }
    public function blocks()
    {
        $username = auth()->user()->username;
        $friendshipController = new FriendshipController();
        $blocks = $friendshipController->getBlockedUsers($username);
        $searchTerm = request('searchblock');
        if ($searchTerm) {
            $blocks->where(function ($query) use ($searchTerm) {
                $query->where('username', 'like', '%' . $searchTerm . '%');
            });
        }
        $blocks = $blocks->paginate(10);


        return view(
            'friend.block',
            [
                'blocks' => $blocks,
            ]
        );
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
            ->take(15)  // Take top 20
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
    //downloading recommendations (energy-based)
    public function downloadPositiveRecommendations()
    {
        $username = auth()->user()->username;

        // Instantiate UserController
        $userController = new UserController();

        // Fetch the recommendations using the method from UserController
        $recommendations = $userController->favPositiveRecomendation($username);

        $jsonData = json_encode($recommendations, JSON_PRETTY_PRINT);
        $filename = "recommendations.json";

        return response($jsonData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename={$filename}"
        ]);
    }

    //downloading recommendations (energy-based)
    public function downloadNegativeRecommendations()
    {
        $username = auth()->user()->username;

        // Instantiate UserController
        $userController = new UserController();

        // Fetch the recommendations using the method from UserController
        $recommendations = $userController->favNegativeRecomendation($username);

        $jsonData = json_encode($recommendations, JSON_PRETTY_PRINT);
        $filename = "recommendations.json";

        return response($jsonData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename={$filename}"
        ]);
    }

    //Stories Functions
    public function top5Songs($username)
    {
        // Remove the month-related code
        $subQuery = SongRating::select('song_id', DB::raw('AVG(rating) as average_rating'))
            ->where('username', $username)
            ->groupBy('song_id')
            ->orderBy('average_rating', 'DESC')
            ->take(5);

        $top5Songs = DB::table('songs')
            ->joinSub($subQuery, 'top_songs', function ($join) {
                $join->on('songs.song_id', '=', 'top_songs.song_id');
            })
            ->get([
                'songs.*',
                'top_songs.average_rating',
            ]);

        //dd($top5Songs);
        //return view('users.user-profile', ['top5Songs' => $top5Songs]);
        return $top5Songs;
    }
    public function SongOfYear($username)
    {
        // Get the current year
        $currentYear = now()->year;

        // Set the condition to get ratings within the current year
        $subQuery = SongRating::select('song_id', DB::raw('AVG(rating) as average_rating'))
            ->where('username', $username)
            ->whereYear('date_rated', $currentYear)
            ->groupBy('song_id')
            ->orderBy('average_rating', 'DESC')
            ->take(1);

        $songOfYear = DB::table('songs')
            ->joinSub($subQuery, 'top_songs', function ($join) {
                $join->on('songs.song_id', '=', 'top_songs.song_id');
            })
            ->get([
                'songs.*',
                'top_songs.average_rating',
            ]);
            
        return $songOfYear;
    }
    public function top5Albums($username)
    {
        // Create a subquery for the average rating of albums by the user
        $ratingSubQuery = DB::table('album_ratings')
            ->select('album_id', DB::raw('AVG(rating) as average_rating'))
            ->where('username', $username)
            ->groupBy('album_id');

        // Join the tables and select the top 10 albums of all time
        $top5Albums = DB::table('albums')
            ->joinSub($ratingSubQuery, 'rating', function ($join) {
                $join->on('albums.album_id', '=', 'rating.album_id');
            })
            ->join('songs', 'albums.album_id', '=', 'songs.album_id')
            ->select('albums.name', 'albums.image_url', 'rating.average_rating')
            ->groupBy('albums.album_id', 'albums.name', 'albums.image_url')
            ->orderBy('average_rating', 'DESC')
            ->take(5)
            ->get();

        return $top5Albums;
    }
    public function favGenres($username)
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
            ->take(5)
            ->all();

        return $topPerformersGenres;
    }    
}
