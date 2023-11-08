<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
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
                $userName = $user->name;
        
                $request->session()->regenerate();
        
                // You can pass the user's name to the view or store it in the session
                $request->session()->put('user_name', $userName);
        
                return redirect('/')->with('message', 'You are now logged in!');
            }
        
            return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
        }
        
    
}