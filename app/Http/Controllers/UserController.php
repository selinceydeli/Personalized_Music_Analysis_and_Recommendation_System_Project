<?php

namespace App\Http\Controllers;

use ReCaptcha\ReCaptcha; // Import the ReCaptcha class at the top
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Show Register/Create Form
    public function create() {
        return view('users.register');
    }

    // Create New User
    public function store(Request $request) {
        $formFields = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);
    
        $response = (new ReCaptcha(env('RECAPTCHA_SECRET_KEY')))->verify($request->input('g-recaptcha-response'));
    
        if ($response->isSuccess()) {
            // Hash Password
            $formFields['password'] = bcrypt($formFields['password']);
    
            $user = User::create($formFields);
    
            // Login
            auth()->login($user);
    
            return redirect('/')->with('message', 'User created and logged in');
        } else {
            return redirect('/register')->with('message', 'reCAPTCHA validation failed');
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
