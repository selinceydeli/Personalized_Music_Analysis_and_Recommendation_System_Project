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

    public function search_id($id){
        $user = user::find($id);
        if(!empty($user)){
            return response()->json($user);
        }
        else{
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
    }

    public function update(Request $request, $id){
        if (User::where('id', $id) -> exists()){
            $user = User::find($id);
            $user->username = is_null($request -> username) ? $user->username : $request->username;
            $user->email = is_null($request -> email) ? $user->email : $request->email;
            $user->email_verified_at = is_null($request -> email_verified_at) ? $user->email_verified_at : $request->email_verified_at;
            $user->name = is_null($request -> name) ? $user->name : $request->name;
            $user->surname = is_null($request -> surname) ? $user->surname : $request->surname;
            $user->password = is_null($request -> password) ? $user->password : $request->password;
            $user->date_of_birth = is_null($request -> date_of_birth) ? $user->date_of_birth : $request->date_of_birth;
            $user->language = is_null($request -> language) ? $user->language : $request->language;
            $user->subscription = is_null($request -> subscription) ? $user->subscription : $request->subscription;
            $user->rate_limit = is_null($request -> rate_limit) ? $user->rate_limit : $request->rate_limit;
            $user->save();
            return response()->json([
                "message" => "User Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "User not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (User::where('id', $id) -> exists()){
            $user = User::find($id);
            $user->delete();
            return response()->json([
                "message" => "User deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
    }
}