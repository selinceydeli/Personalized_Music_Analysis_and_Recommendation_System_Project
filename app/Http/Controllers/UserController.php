<?php

namespace App\Http\Controllers;

use ReCaptcha\ReCaptcha; // Import the ReCaptcha class at the top
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request){
        $formFields = $request->validate([
            'name' => ['required', 'min:3'],
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
                $request->session()->regenerate();
                return redirect('/')->with('message', 'You are now logged in!');
            }
        
            return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
        }
    
}