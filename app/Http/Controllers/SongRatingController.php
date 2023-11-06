<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SongRating;
use App\Http\Resources\SongRatingResource;

class SongRatingController extends Controller
{
    public function index(){
        $songratings = songRating::all();
        return response()->json($songratings);
    }

    public function store(Request $request){
        $songrating = new SongRating;
        $songrating->rating = $request->rating;
        $songrating->username = $request->username;
        $songrating->song_id = $request->song_id;
        $songrating->date_rated = $request->date_rated;
        $songrating->save();
        return response()->json([
            "message" => "Song rating added"
        ], 200);
    }

    public function search_id_song($id){

        $songratings = SongRating::where('song_id', 'EQUALS', "{$id}")->get();

        return SongRatingResource::collection($songratings);
    }

    public function search_id_user($username){

        $songratings = SongRating::where('username', 'EQUALS', "{$username}")->get();

        return SongRatingResource::collection($songratings);
    }

    public function update(Request $request, $id){
        if (SongRating::where('id', $id) -> exists()){
            $songrating = SongRating::find($id);
            $songrating->rating = is_null($request -> rating) ? $songrating->rating : $request->rating;
            $songrating->username = is_null($request -> username) ? $songrating->username : $request->username;
            $songrating->song_id = is_null($request -> song_id) ? $songrating->song_id : $request->song_id;
            $songrating->date_rated = is_null($request -> date_rated) ? $songrating->date_rated : $request->date_rated;
            $songrating->save();
            return response()->json([
                "message" => "Song rating Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Song rating not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (SongRating::where('id', $id) -> exists()){
            $songrating = SongRating::find($id);
            $songrating->delete();
            return response()->json([
                "message" => "Song rating deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Song rating not found"
            ], 404);
        }
    }
}