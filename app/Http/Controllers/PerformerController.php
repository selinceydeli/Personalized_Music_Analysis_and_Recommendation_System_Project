<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Performer;
use App\Http\Resources\PerformerResource;
use PDO;

class PerformerController extends Controller
{
    public function index(){
        $performers = Performer::all();
        return response()->json($performers);
    }

    public function store(Request $request){
        $performer = new Performer;
        $performer->name = $request->name;
        $performer->genre = $request->genre;
        $performer->popularity = $request->popularity;
        $performer->image_url = $request->image_url;
        $performer->save();
        return response()->json([
            "message" => "Performer added"
        ], 200);
    }

    public function search_id($id){
        $performer = Performer::find($id);
        if(!empty($performer)){
            return response()->json($performer);
        }
        else{
            return response()->json([
                "message" => "Performer not found"
            ], 404);
        }
    }

    public function search_name($searchTerm)
    {
        $performers = Performer::where('name', 'LIKE', "%{$searchTerm}%")->get();

        return PerformerResource::collection($performers);
    }

    public function update(Request $request, $id){
        if (Performer::where('artist_id', $id) -> exists()){
            $performer = Performer::find($id);
            $performer->name = is_null($request -> name) ? $performer->name : $request->name;
            $performer->genre = is_null($request -> genre) ? $performer->genre : $request->genre;
            $performer->popularity = is_null($request -> popularity) ? $performer->popularity : $request->popularity;
            $performer->image_url = is_null($request -> image_url) ? $performer->image_url : $request->image_url;
            $performer->save();
            return response()->json([
                "message" => "Performer Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "performer not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (Performer::where('artist_id', $id) -> exists()){
            $performer = Performer::find($id);
            $performer->delete();
            return response()->json([
                "message" => "Performer deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Performer not found"
            ], 404);
        }
    }
}