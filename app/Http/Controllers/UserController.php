<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request){
        $user = new User;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->email_verified_at = $request->email_verified_at;
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->password = $request->password;
        $user->date_of_birth = $request->date_of_birth;
        $user->language = $request->language;
        $user->subscription = $request->subscription;
        $user->rate_limit = $request->rate_limit;
        $user->save();
        return response()->json([
            "message" => "User added"
        ], 200);
    }

    // Instead of search_id() method, a search_username() method is defined
    // since the primary key of the Users table is username
    public function search_username($username){
        $user = User::where('username', $username)->first();
        if($user){
            return response()->json($user);
        } else {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
    }    
}