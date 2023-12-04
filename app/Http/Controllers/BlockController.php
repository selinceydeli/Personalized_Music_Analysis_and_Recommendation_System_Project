<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;

class BlockController extends Controller
{
    public function blockUser($blockedUsername)
    {
        $block = Block::create([
            'blocker_username' => auth()->user()->username,
            'blocked_username' => $blockedUsername,
        ]);

        return response()->json([
            "message" => "User Blocked"
        ], 200);
    }

    public function unblockUser($blockedUsername)
    {
        Block::where('blocker_id', auth()->user()->username)
            ->where('blocked_id', $blockedUsername)
            ->delete();

            return response()->json([
                "message" => "User Unblocked"
            ], 200);
    }
}
