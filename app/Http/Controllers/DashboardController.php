<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Display the dashboard page
        return view('listings.dashboard');
    }

    public function settings()
    {
        // Display the settings page
        return view('listings.settings');
    }

    public function profile()
    {
        // Display the user profile page
        return view('listings.profile');
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'You have been logged out!');
    }
}

