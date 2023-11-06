<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlbumRating;
use App\Http\Resources\AlbumRatingResource;

class AlbumRatingController extends Controller
{
    public function index(){
        $albumratings = AlbumRating::all();
        return response()->json($albumratings);
    }

    public function store(Request $request){
        $albumrating = new AlbumRating;
        $albumrating->rating = $request->rating;
        $albumrating->username = $request->username;
        $albumrating->album_id = $request->album_id;
        $albumrating->date_rated = $request->date_rated;
        $albumrating->save();
        return response()->json([
            "message" => "Album rating added"
        ], 200);
    }


    public function search_id_album($id){

        $albumratings = AlbumRating::where('album_id', 'EQUALS', "{$id}")->get();

        return AlbumRatingResource::collection($albumratings);
    }

    public function search_id_user($username){

        $albumratings = AlbumRating::where('username', 'EQUALS', "{$username}")->get();

        return AlbumRatingResource::collection($albumratings);
    }

    public function update(Request $request, $id){
        if (AlbumRating::where('id', $id) -> exists()){
            $albumrating = AlbumRating::find($id);
            $albumrating->rating = is_null($request -> rating) ? $albumrating->rating : $request->rating;
            $albumrating->username = is_null($request -> username) ? $albumrating->username : $request->username;
            $albumrating->album_id = is_null($request -> album_id) ? $albumrating->album_id : $request->album_id;
            $albumrating->date_rated = is_null($request -> date_rated) ? $albumrating->date_rated : $request->date_rated;
            $albumrating->save();
            return response()->json([
                "message" => "Album rating Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Album rating not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (AlbumRating::where('id', $id) -> exists()){
            $albumrating = AlbumRating::find($id);
            $albumrating->delete();
            return response()->json([
                "message" => "Album rating deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Album rating not found"
            ], 404);
        }
    }
}