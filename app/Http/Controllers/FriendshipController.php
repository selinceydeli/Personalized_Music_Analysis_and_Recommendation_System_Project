<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendRequestReceived;

class FriendshipController extends Controller
{
    public function sendRequest(User $user) {
        $friendship = Friendship::create([
            'requester' => auth()->user()->username,
            'user_requested' => $user->username,
            'status' => 0,  // 0 for pending
        ]);

        $user->notify(new FriendRequestReceived(auth()->user()));

        return response()->json([
            "message" => "Request Sent"
        ], 200);
    }

    public function acceptRequest(Friendship $friendship) {
        $friendship->update(['status' => 1]);  // 1 for accepted

        return response()->json([
            "message" => "Request Accepted"
        ], 200);
    }
}
