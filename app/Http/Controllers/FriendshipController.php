<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\Block;
use App\Models\User;

class FriendshipController extends Controller
{
    public function sendRequestWeb(User $user) {
        $currentUser = auth()->user()->username;
        $block = Block::where('blocker_username', $requester)
                             ->where('blocked_username', $userequested)
                             ->first();
        
        $block2 = Block::where('blocker_username', $userequested)
                        ->where('blocked_username', $requester)
                        ->first();

        $friendship = Friendship::create([
            'requester' => $currentUser,
            'user_requested' => $user->username,
            'status' => 0,  // 0 for pending
        ]);

        return response()->json([
            "message" => "Request Sent"
        ], 200);
    }

    public function sendRequestMobile(User $requester, User $userequested) {
        $block = Block::where('blocker_username', $requester)
                             ->where('blocked_username', $userequested)
                             ->first();
        
        $block2 = Block::where('blocker_username', $userequested)
                        ->where('blocked_username', $requester)
                        ->first();

        if($block){
            return response()->json([
                "message" => "You have previously blocked this user!"
            ], 200);
        }

        if($block2){
            return response()->json([
                "message" => "This user has blocked you"
            ], 200);
        }

        $friendship = Friendship::create([
            'requester' => $requester->username,
            'user_requested' => $userequested->username,
            'status' => 0,  // 0 for pending
        ]);

        return response()->json([
            "message" => "Request Sent"
        ], 200);
    }

    public function acceptRequest(Request $request) {

        $requester = $request->input('requester');
        $userRequested = $request->input('user_requested');

        $friendship2 = Friendship::where('requester', $requester)
                             ->where('user_requested', $userRequested)
                             ->where('status', 0)
                             ->first();
        if ($friendship2) {
            $friendship2->status = 1;
            $friendship2->save(); // This will update the record in the database

            return response()->json([
                "message" => "Request Accepted"
            ], 200);
        } else {
            return response()->json([
                "message" => "Friendship not found or already accepted"
            ], 404);
        }
    }

    public function seeRequests(){
        $username = auth()->user()->username;
        $requesterUsernames = Friendship::where('user_requested', $username)
                                     ->where('status', 0) // If you want only accepted requests
                                     ->pluck('requester');

        return response()->json($requesterUsernames);
    }

    public function seeRequestsMobile($username){
        $requesterUsernames = Friendship::where('user_requested', $username)
                                     ->where('status', 0) // If you want only accepted requests
                                     ->pluck('requester');

        return response()->json($requesterUsernames);
    }
}
