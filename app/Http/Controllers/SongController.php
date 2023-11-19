<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Http\Resources\SongResource;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function index(){
        $songs = Song::all();
        return response()->json($songs);
    }

    public function store(Request $request){
        $song = new Song;
        $song->name = $request->name;
        $song->performers = $request->performers;
        $song->isrc = $request->isrc;
        $song->duration = $request->duration;
        $song->lyrics = $request->lyrics;
        $song->explicit = $request->explicit;
        $song->tempo = $request->tempo;
        $song->key = $request->key;
        $song->mode = $request->mode;
        $song->system_entry_date = $request->system_entry_date;
        $song->album_id = $request->album_id;
        $song->danceability = $request->danceability;
        $song->energy = $request->energy;
        $song->loudness = $request->loudness;
        $song->speechiness = $request->speechiness;
        $song->instrumentalness = $request->instrumentalness;
        $song->liveness = $request->liveness;
        $song->time_signature = $request->time_signature;
        $song->valence = $request->valence;
        $song->save();
        return response()->json([
            "message" => "Song added"
        ], 200);
    }

    public function search_id($id){
        $song = Song::find($id);
        if(!empty($song)){
            return response()->json($song);
        }
        else{
            return response()->json([
                "message" => "Song not found"
            ], 404);
        }
    }

    /*
    returns all songs that contain the given searchTerm within their name
    */
    public function search_name($searchTerm)
    {
        $songs = Song::where('name', 'LIKE', "%{$searchTerm}%")->get();

        return SongResource::collection($songs);
    }

    public function update(Request $request, $id){
        if (Song::where('song_id', $id) -> exists()){
            $song = Song::find($id);
            $song->name = is_null($request -> name) ? $song->name : $request->name;
            $song->lyrics = is_null($request -> lyrics) ? $song->lyrics : $request->lyrics;
            $song->isrc = is_null($request -> isrc) ? $song->isrc : $request->isrc;
            $song->performers = is_null($request -> performers) ? $song->performers : $request->performers;
            $song->tempo = is_null($request -> tempo) ? $song->tempo : $request->tempo;
            $song->key = is_null($request -> key) ? $song->key : $request->key;
            $song->mode = is_null($request -> mode) ? $song->mode : $request->mode;
            $song->system_entry_date = is_null($request -> system_entry_date) ? $song->system_entry_date : $request->system_entry_date;
            $song->album_id = is_null($request -> album_id) ? $song->album_id : $request->album_id;
            $song->explicit = is_null($request -> explicit) ? $song->explicit : $request->explicit;
            $song->duration = is_null($request -> duration) ? $song->duration : $request->duration;
            $song->danceability = is_null($request -> danceability) ? $song->danceability : $request->danceability;
            $song->energy = is_null($request -> energy) ? $song->energy : $request->energy;
            $song->loudness = is_null($request -> loudness) ? $song->loudness : $request->loudness;
            $song->speechiness = is_null($request -> speechiness) ? $song->speechiness : $request->speechiness;
            $song->instrumentalness = is_null($request -> instrumentalness) ? $song->instrumentalness : $request->instrumentalness;
            $song->liveness = is_null($request -> liveness) ? $song->liveness : $request->liveness;
            $song->time_signature = is_null($request -> time_signature) ? $song->time_signature : $request->time_signature;
            $song->valence = is_null($request -> valence) ? $song->valence : $request->valence;
            $song->save();
            return response()->json([
                "message" => "Song Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Song not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (Song::where('song_id', $id) -> exists()){
            $song = Song::find($id);
            $song->delete();
            return response()->json([
                "message" => "Song deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Song not found"
            ], 404);
        }
    }
}