<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformerRating;
use App\Http\Resources\PerformerRatingResource;


class PerformerRatingController extends Controller
{
    public function index(){
        $performerratings = PerformerRating::all();
        return response()->json($performerratings);
    }

    public function store(Request $request){
        $performerrating = new PerformerRating;
        $performerrating->rating = $request->rating;
        $performerrating->username = $request->username;
        $performerrating->performer_id = $request->performer_id;
        $performerrating->date_rated = $request->date_rated;
        $performerrating->save();
        return response()->json([
            "message" => "Performer rating added"
        ], 200);
    }

    public function search_id_performer($id){

        $performerratings = PerformerRating::where('performer_id', 'EQUALS', "{$id}")->get();

        return PerformerRatingResource::collection($performerratings);
    }

    public function search_id_user($username){

        $performerratings = PerformerRating::where('username', 'EQUALS', "{$username}")->get();

        return PerformerRatingResource::collection($performerratings);
    }

    public function update(Request $request, $id){
        if (PerformerRating::where('id', $id) -> exists()){
            $performerrating = PerformerRating::find($id);
            $performerrating->rating = is_null($request -> rating) ? $performerrating->rating : $request->rating;
            $performerrating->username = is_null($request -> username) ? $performerrating->username : $request->username;
            $performerrating->performer_id = is_null($request -> performer_id) ? $performerrating->performer_id : $request->performer_id;
            $performerrating->date_rated = is_null($request -> date_rated) ? $performerrating->date_rated : $request->date_rated;
            $performerrating->save();
            return response()->json([
                "message" => "Performer rating Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Performer rating not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (PerformerRating::where('id', $id) -> exists()){
            $performerrating = PerformerRating::find($id);
            $performerrating->delete();
            return response()->json([
                "message" => "Performer rating deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Performer rating not found"
            ], 404);
        }
    }
}
