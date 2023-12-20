<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\Friendship;

class BlockController extends Controller
{
    public function blockUser($blockedUsername)
    {
        $block = Block::create([
            'blocker_username' => auth()->user()->username,
            'blocked_username' => $blockedUsername,
        ]);

        $friendship =  Friendship::where('requester', $blockedUsername)
                        ->where('user_requested', auth()->user()->username)
                        ->first();

        $friendship2 = Friendship::where('requester', auth()->user()->username)
                        ->where('user_requested', $blockedUsername)
                        ->first();
        if($friendship){
            $friendship->delete();
        }
        if($friendship2){ 
            $friendship2->delete();
        }

        return response()->json([
            "message" => "User Blocked"
        ], 200);
    }

    public function blockUserMobile(Request $request)
    {

        $blockerUsername = $request->input('blocker_username');
        $blockedUsername = $request->input('blocked_username');

        $block = Block::create([
            'blocker_username' => $blockerUsername,
            'blocked_username' => $blockedUsername,
        ]);

        $friendship =  Friendship::where('requester', $blockedUsername)
                        ->where('user_requested', $blockerUsername)
                        ->first();

        $friendship2 = Friendship::where('requester', $blockerUsername)
                        ->where('user_requested', $blockedUsername)
                        ->first();
        if($friendship){
            $friendship->delete();
        }
        if($friendship2){ 
            $friendship2->delete();
        }

        return response()->json([
            "message" => "User Blocked"
        ], 200);
    }

    public function unblockUser($blockedUsername)
    {
        Block::where('blocker_username', auth()->user()->username)
            ->where('blocked_username', $blockedUsername)
            ->delete();

        return response()->json([
            "message" => "User Unblocked"
        ], 200);
    }

    public function unblockUserMobile(Request $request)
    {
        $blockerUsername = $request->input('blocker_username');
        $blockedUsername = $request->input('blocked_username');

        Block::where('blocker_username', $blockerUsername)
            ->where('blocked_username', $blockedUsername)
            ->delete();

        return response()->json([
            "message" => "User Unblocked"
        ], 200);
    }
}
