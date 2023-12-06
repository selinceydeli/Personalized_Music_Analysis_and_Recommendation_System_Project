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
                    'theme' => $user->theme, // Add this line to include the 'theme' key

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

    public function update(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'language' => 'required|in:English,Spanish,Mandarin Chinese,Hindi,Arabic,Portuguese,Bengali,Russian,Japanese,Punjabi,German,Turkish',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Update the user's information
        $user->name = $validatedData['name'];
        $user->surname = $validatedData['surname'];
        $user->email = $validatedData['email'];

        // Update the password if provided
        if (!empty($validatedData['password'])) {
            $user->password = bcrypt($validatedData['password']);
        }

        // Update the language
        $user->language = $validatedData['language'];

         // Update the theme preference
        $user->theme = $request->input('theme', 'light'); // Default to light if not provided

        // Save the changes
        $user->save();

        // Redirect back to the settings page with a success message
        return redirect()->route('settings')->with('success', 'User information updated successfully.');
    }

    public function subscriptionUpdate(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Check if the user is upgrading or downgrading the subscription
        if ($user->subscription === 'free') {
            // Upgrade to Premium
            $user->subscription = 'premium';
            $user->rateLimit = 1000; // Set the new rate limit for Premium (adjust as needed)
        } elseif ($user->subscription === 'premium') {
            // Downgrade to Free
            $user->subscription = 'free';
            $user->rateLimit = 500; // Set the new rate limit for Free (adjust as needed)
        }

        // Save the changes
        $user->save();

        // Redirect back to the settings page with a success message
        return redirect()->route('settings')->with('success', 'Subscription updated successfully.');
    }
}
