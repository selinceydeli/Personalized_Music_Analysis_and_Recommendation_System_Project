<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'language' => ['required', 'string', 'max:255'],
            'subscription' => ['required', 'string', 'max:255'],
            'rate_limit' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            // Add more validation rules for other fields
        ]);

        // Update user profile
        $user->update($validatedData);

        return redirect('/profile')->with('success', 'Profile updated successfully');
    }

    public function favorite10RatingsAllTime(Request $request)
    {
        $username = auth()->user()->username;

        // Remove the month-related code
        // $monthSelect = intval($request->input('monthSelect', 6));
        // $monthArray = [1, 3, 6, 12];
        // $MonthsAgo = now()->subMonths($monthSelect);

        // Create a subquery to get the top 10 rated, unique song IDs for all time
        $subQuery = SongRating::select('song_id', DB::raw('AVG(rating) as average_rating'))
            ->where('username', $username)
            // Remove the condition related to months
            // ->where('date_rated', '>=', $MonthsAgo)
            ->groupBy('song_id')
            ->orderBy('average_rating', 'DESC')
            ->take(10);

        // Perform a join to get the song information based on the top song IDs
        $topSongs = DB::table('songs')
            ->joinSub($subQuery, 'top_songs', function ($join) {
                $join->on('songs.song_id', '=', 'top_songs.song_id');
            })
            ->get([
                'songs.*', // Select all fields from songs
                // Here you can also join other song details if needed
                'top_songs.average_rating', // Get the average rating as well
            ]);

        return view('users.user-profile',  ['topSongs' => $topSongs]);

        // Remove month-related variables from the view
        // return view('analysis.favorite_songs', ['topSongs' => $topSongs, 'monthArray' => $monthArray, 'monthSelect' => $monthSelect]);
        // return response()->json($topSongs);
    }

}


