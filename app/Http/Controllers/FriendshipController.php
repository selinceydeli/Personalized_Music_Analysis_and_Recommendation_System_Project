<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendRequestReceived;

class FriendshipController extends Controller
{
    public function sendRequestWeb(User $user) {
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

    public function sendRequestMobile(User $requester, User $userequested) {
        $friendship = Friendship::create([
            'requester' => $requester->username,
            'user_requested' => $userequested->username,
            'status' => 0,  // 0 for pending
        ]);

        $userequested->notify(new FriendRequestReceived($requester));

        return response()->json([
            "message" => "Request Sent"
        ], 200);
    }

    public function acceptRequest(Friendship $friendship) {
        
        $friendship2 = Friendship::where('requester', $friendship->requester)
                                    ->where('user_requested', $friendship->userequester)
                                    ->where('status', 0)->first();
        $friendship2->status = 1;

        return response()->json([
            "message" => "Request Accepted"
        ], 200);
    }
}
