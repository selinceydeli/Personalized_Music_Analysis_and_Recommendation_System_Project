<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use App\Http\Resources\SongResource;
use App\Models\Performer;
use App\Models\PerformerRating;
use App\Models\Song;
use Illuminate\Support\Facades\DB;
use ReCaptcha\ReCaptcha; // Import the ReCaptcha class at the top

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return response()->json($users);
    }

    // Show Register/Create Form
    public function create() {
        return view('users.register');
    }

    public function store(Request $request){
        $formFields = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:255', Rule::unique('users', 'username')],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'language' => ['required', 'string', 'max:255'],
            'subscription' => ['required', 'string', 'max:255'],
            'rate_limit' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);
        $response = (new ReCaptcha(env('RECAPTCHA_SECRET_KEY')))->verify($request->input('g-recaptcha-response'));

        if ($response->isSuccess()) {
            $formFields['password'] = bcrypt($formFields['password']);
            $user = new User;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->email_verified_at = $request->email_verified_at;
            $user->name = $request->name;
            $user->surname = $request->surname;
            $user->password = $formFields['password'];
            $user->date_of_birth = $request->date_of_birth;
            $user->language = $request->language;
            $user->subscription = $request->subscription;
            $user->rate_limit = $request->rate_limit;
            $user->save();
            auth()->login($user);
    
            return redirect('/')->with('message', 'User created and logged in');
        } else {
            return redirect('/register')->with('message', 'reCAPTCHA validation failed');
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
        $recommendedSongs = Song::where(function ($query) use ($similarPerformers) {
            foreach ($similarPerformers as $artistId) {
                // Adjust the query to check if the JSON array contains the artistId as a string
                $query->orWhereJsonContains('performers', (string)$artistId);
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
    //Logout User

    public function logout(Request $request) {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'You have been logged out!');
    }

    // Show Login Form

    public function login() {
        return view('users.login');
    }

    // Authenticate User

    public function authenticate(Request $request) {
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);
    
        $response = (new ReCaptcha(env('RECAPTCHA_SECRET_KEY')))->verify($request->input('g-recaptcha-response'));
    
        if ($response->isSuccess() && auth()->attempt($formFields)) {
            // Get the authenticated user
            $user = auth()->user();
    
            // Retrieve the user's name from the database
            $userName = $user->username;
    
            $request->session()->regenerate();
    
            // You can pass the user's name to the view or store it in the session
            $request->session()->put('user_name', $userName);
    
            return redirect('/')->with('message', 'You are now logged in!');
        }
    
        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }
    

>>>>>>>>> Temporary merge branch 2
}