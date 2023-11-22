<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class SubscriptionController extends Controller
{
    public function show()
    {
        // Add logic to fetch and display subscription preferences
        return view('subscription.show');
    }


    public function upgrade()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Add logic to update subscription type
        $user->subscription = 'Premium'; // Change this to your premium subscription type
        $user->save();

        return redirect()->route('subscription.show')->with('success', 'Subscription upgraded to Premium!');
    }
}
