<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class SubscriptionController extends Controller
{
    public function show()
    {
        // Add logic to fetch and display subscription preferences
        return view('subscription.show');
    }

    public function plans()
    {
        $user = auth()->user();
        return view('subscription.plans', [
            'user' => $user,
        ]);
    }
    public function free(Request $request) {
        $user = auth()->user();
        $plan = $request->input('plan');
        $user['subscription'] = $plan;
        $user['rate_limit'] = 100;
        $user->save();
        return redirect('/settings')->with('message', 'Subscription Updated!');
    }

    public function pay(Request $request)
    {
        $user = auth()->user();
        $plan = $request->input('plan');
    
        // Update the user's subscription plan attribute
        $user->subscription = $plan;
    
        // Set rate limit based on the plan
        switch ($plan) {
            case 'free':
                $user->rate_limit = 100;
                break;
            case 'silver':
                $user->rate_limit = 1000;
                break;
            case 'gold':
                $user->rate_limit = 5000;
                break;
        }

    
        $user->save();
    
        // Redirect with success message
        return redirect('/settings')->with('message', 'Payment succeeded. Subscription updated!');
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
