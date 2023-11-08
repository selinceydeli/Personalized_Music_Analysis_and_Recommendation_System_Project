<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
{
    // Display the dashboard page
    return view('listings.dashboard');
}

public function playlists()
{
    // Display the playlists page
    return view('listings.playlists');
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

}
