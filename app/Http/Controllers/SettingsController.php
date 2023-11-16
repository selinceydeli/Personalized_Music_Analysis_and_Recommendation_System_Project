<?php

// app/Http/Controllers/SettingsController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        // Check if the user is authenticated
        if (auth()->check()) {
            $user = auth()->user(); // Get the authenticated user

            $data = [
                'userInfo' => [
                    'username' => $user->username,
                    'dob' => $user->date_of_birth,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'email' => $user->email,
                    'password' => null, 
                ],
                'language' => [
                    'current' => $user->language,
                    'options' => ["English",
                    "Spanish",
                    "Mandarin Chinese",
                    "Hindi",
                    "Arabic",
                    "Portuguese",
                    "Bengali",
                    "Russian",
                    "Japanese",
                    "Punjabi",
                    "German",
                    "Japanese",
                    "Turkish"], // Add language options here
                ],
                'subscription' => [
                    'current' => $user->subscription,
                    'rateLimit' => $user->rateLimit,
                ],
            ];

            return view('settings', compact('data'));
        } else {
            // Redirect to the login page or handle the case where the user is not authenticated
            return redirect()->route('login');
        }
    }
}
