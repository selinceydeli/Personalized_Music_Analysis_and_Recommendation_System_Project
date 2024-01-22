<?php
// app/Http/Controllers/SettingsController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    public function index()
    {
        // Check if the user is authenticated
        if (auth()->check()) {
            $user = auth()->user(); // Get the authenticated user

            // Check if the 'explicit' key is set in the session
            if (Session::has('explicit')) {
                // The 'explicit' key is set in the session
                $explicit = Session::get('explicit');
                // Use $explicit as needed
            } else {
                // The 'explicit' key is not set in the session
                // You can set a default value or take appropriate action
                $explicit = true;
            }

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
                    'options' => [
                        "English",
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
                        "Turkish"
                    ], // Add language options here
                ],
                'subscription' => [
                    'current' => $user->subscription,
                    'rateLimit' => $user->rateLimit,
                ],
            ];

            return view('settings', compact('data', 'user', 'explicit'));
        } else {
            // Redirect to the login page or handle the case where the user is not authenticated
            return redirect()->route('login');
        }
    }

    public function updateExplicit(Request $request)
    {
        $explicit = $request->input('explicit', false);

        // Update the session variable
        Session::put('explicit', $explicit);

        return response()->json(['success' => true]);
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

        session(['explicit' => $request->input('child_mode') === 'on']);

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
