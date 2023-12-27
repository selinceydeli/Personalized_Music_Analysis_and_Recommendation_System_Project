<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Block;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FriendshipController extends Controller
{
    public function sendRequestWeb($user)
    {
        $currentUser = auth()->user()->username;
        $block = Block::where('blocker_username', $currentUser)
            ->where('blocked_username', $user)
            ->first();

        $block2 = Block::where('blocker_username', $user)
            ->where('blocked_username', $currentUser)
            ->first();

        if ($block) {
            return response()->json([
                "message" => "You have previously blocked this user!"
            ], 200);
        }

        if ($block2) {
            return response()->json([
                "message" => "This user has blocked you"
            ], 200);
        }

        $friendship = Friendship::create([
            'requester' => $currentUser,
            'user_requested' => $user,
            'status' => 0,  // 0 for pending
        ]);


        return back()->with('message', 'Request Send');
    }

    public function acceptRequest(Request $request)
    {

        $requester = $request->input('user_to_accept');
        $userRequested = auth()->user()->username;

        $friendship2 = Friendship::where('requester', $requester)
            ->where('user_requested', $userRequested)
            ->where('status', 0)
            ->first();
        if ($friendship2) {
            $friendship2->status = 1;
            $friendship2->save(); // This will update the record in the database

            return back()->with('message', 'Request Accepted');
        } else {
            return response()->json([
                "message" => "Friendship not found or already accepted"
            ], 404);
        }
    }

    public function seeRequests()
    {
        $username = auth()->user()->username;


        $requesterUsernames = Friendship::where('user_requested', $username)
            ->where('status', 0)
            ->pluck('requester');

        $requests = User::whereIn('username', $requesterUsernames)
            ->withCount('friendsOfMine')
            ->orderByDesc('friends_of_mine_count');

        return $requests;
    }

    public function unfriend($user)
    {
        $friendship2 = Friendship::where('requester', $user)
            ->where('user_requested', auth()->user()->username)
            ->where('status', 1)
            ->first();

        if ($friendship2) {
            $friendship2->delete();
            return back()->with('message', 'Unfollowed');
        }
        return response()->json([
            "message" => "You don't follow this user"
        ], 200);
    }
    public function unrequest(Request $request)
    {
        $unrequester = $request->input('user_to_unrequest');
        $friendship2 = Friendship::where('requester', auth()->user()->username)
            ->where('user_requested', $unrequester)
            ->where('status', 0)
            ->first();


        if ($friendship2) {
            $friendship2->delete();
            return back()->with('message', 'Request taken back');
        }
    }

    public function rejectRequest(Request $request)
    {
        $requester = $request->input('user_to_reject');
        $friendship2 = Friendship::where('requester', $requester)
            ->where('user_requested', auth()->user()->username)
            ->where('status', 0)
            ->first();

        if ($friendship2) {
            $friendship2->delete();
            return back()->with('message', 'Request Rejected');
        }
        return response()->json([
            "message" => "You don't follow this user"
        ], 200);
    }

    public function block(Request $request)
    {
        $blockedUser = $request->input('user_to_block');
        $blocker = auth()->user()->username;

        // Check and delete pending friend requests from the blocked user
        $friendship2 = Friendship::where('requester', $blocker)
            ->where('user_requested', $blockedUser)
            ->where('status', 0) // Pending requests
            ->first();

        if ($friendship2) {
            $friendship2->delete();
        }
        // Check and delete pending friend requests from the blocked user
        $friendship2 = Friendship::where('requester', $blockedUser)
            ->where('user_requested', $blocker)
            ->where('status', 0) // Pending requests
            ->first();

        if ($friendship2) {
            $friendship2->delete();
        }

        // Block the user
        $block = Block::create([
            'blocker_username' => $blocker,
            'blocked_username' => $blockedUser
        ]);

        return back()->with('message', 'User blocked');
    }

    public function unblock(Request $request)
    {
        $username = auth()->user()->username;
        $unblocked = $request->input('user_to_unblock');
        $block2 = Block::where('blocker_username', $username)
            ->where('blocked_username', $unblocked)
            ->first();

        if ($block2) {
            $block2->delete();
            return back()->with('message', 'User Unblocked');
        }

        $block2 = Block::where('blocker_username', $unblocked)
            ->where('blocked_username', $username)
            ->first();

        if ($block2) {
            $block2->delete();
            return back()->with('message', 'User Unblocked');
        }
        return response()->json([
            "message" => "You don't follow this user"
        ], 200);
    }


    public function getPendingFriendRequests($username)
    {
        $pendingRequests = Friendship::where('requester', $username)
            ->where('status', 0) // 0 for pending requests
            ->get();

        $pendingRequestsDictionary = [];

        foreach ($pendingRequests as $request) {
            // Store the user_requested as key and the User object as value in the dictionary
            $pendingRequestsDictionary[$request->user_requested] = $request->userRequested;
        }

        return $pendingRequestsDictionary;
    }

    public function getAllFriends($username): Builder
    {
        // Get the friends where the current user sent the request and it's accepted
        $friendsRequestedByUser = Friendship::where('requester', $username)
            ->where('status', 1); // 1 for accepted requests

        // Get the friends where the current user was requested and it's accepted
        $friendsRequestedToUser = Friendship::where('user_requested', $username)
            ->where('status', 1); // 1 for accepted requests

        // Get blocked users
        $blockedUsers = Block::where('blocker_username', $username)
            ->pluck('blocked_username');

        // Get non-friends and non-blocked users with friend counts
        $nonFriendsAndNotBlocked = User::whereNotIn('username', function ($query) use ($username, $friendsRequestedByUser, $friendsRequestedToUser, $blockedUsers) {
            $query->select('username')
                ->from('users')
                ->whereIn('username', $friendsRequestedByUser->pluck('user_requested')
                    ->merge($friendsRequestedToUser->pluck('requester'))
                    ->unique())
                ->orWhereIn('username', $blockedUsers);
        })
            ->where('username', '!=', $username) // Exclude the current user
            ->withCount('friendsOfMine')
            ->orderByDesc('friends_of_mine_count'); // Retrieve the results

        return $nonFriendsAndNotBlocked;
    }

    public function getFriends($username): Builder
    {
        // Fetch all friends for the given username
        $friends = User::select('users.*')
            ->join('friendships', function ($join) use ($username) {
                $join->on('users.username', '=', 'friendships.requester')
                    ->where('friendships.user_requested', '=', $username)
                    ->where('friendships.status', 1); // Consider only accepted friendships
            })
            ->orWhere(function ($query) use ($username) {
                $query->join('friendships', function ($join) use ($username) {
                    $join->on('users.username', '=', 'friendships.user_requested')
                        ->where('friendships.requester', '=', $username)
                        ->where('friendships.status', 1); // Consider only accepted friendships
                });
            });

        return $friends;
    }





    public function getBlockedUsers($username)
    {
        // Retrieve the blocked users for the given $username
        $blockedUsers = Block::where('blocker_username', $username)->pluck('blocked_username');

        // Fetch users who are blocked and count their friends
        $usersWithFriendCounts = User::select('users.*')
            ->whereIn('users.username', $blockedUsers)
            ->leftJoin('friendships', function ($join) use ($username) {
                $join->on('friendships.user_requested', '=', 'users.username')
                    ->where('friendships.status', 1)
                    ->where('friendships.requester', '!=', $username); // Exclude the blocker user
            })
            ->orWhere(function ($query) use ($username) {
                $query->leftJoin('friendships', function ($join) use ($username) {
                    $join->on('friendships.requester', '=', 'users.username')
                        ->where('friendships.status', 1)
                        ->where('friendships.user_requested', '!=', $username); // Exclude the blocker user
                });
            })
            ->groupBy('users.username')
            ->withCount('friendsOfMine')
            ->orderByDesc('friends_of_mine_count');

        return $usersWithFriendCounts;
    }
}
