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
        $song->publ_date = $request->publ_date;
        $song->performers = $request->performers;
        $song->song_writer = $request->song_writer;
        $song->genre = $request->genre;
        $song->recording_type = $request->recording_type;
        $song->song_length_seconds = $request->song_length_seconds;
        $song->tempo = $request->tempo;
        $song->danceability = $request->danceability;
        $song->energy = $request->energy;
        $song->key = $request->key;
        $song->loudness = $request->loudness;
        $song->speechiness = $request->speechiness;
        $song->acousticness = $request->acousticness;
        $song->instrumentalness = $request->instrumentalness;
        $song->liveness = $request->liveness;
        $song->valence = $request->valence;
        $song->mood = $request->mood;
        $song->language = $request->language;
        $song->system_entry_date = $request->system_entry_date;
        $song->album_id = $request->album_id;
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
        if (Song::where('id', $id) -> exists()){
            $song = Song::find($id);
            $song->name = is_null($request -> name) ? $song->name : $request->name;
            $song->publ_date = is_null($request -> publ_date) ? $song->publ_date : $request->publ_date;
            $song->performers = is_null($request -> performers) ? $song->performers : $request->performers;
            $song->song_writer = is_null($request -> song_writer) ? $song->song_writer : $request->song_writer;
            $song->genre = is_null($request -> genre) ? $song->genre : $request->genre;
            $song->recording_type = is_null($request -> recording_type) ? $song->recording_type : $request->recording_type;
            $song->song_length_seconds = is_null($request -> song_length_seconds) ? $song->song_length_seconds : $request->song_length_seconds;
            $song->tempo = is_null($request -> tempo) ? $song->tempo : $request->tempo;
            $song->danceability = is_null($request -> danceability) ? $song->danceability : $request->danceability;
            $song->energy = is_null($request -> energy) ? $song->energy : $request->energy;
            $song->key = is_null($request -> key) ? $song->key : $request->key;
            $song->loudness = is_null($request -> loudness) ? $song->loudness : $request->loudness;
            $song->speechiness = is_null($request -> speechiness) ? $song->speechiness : $request->speechiness;
            $song->acousticness = is_null($request -> acousticness) ? $song->acousticness : $request->acousticness;
            $song->instrumentalness = is_null($request -> instrumentalness) ? $song->instrumentalness : $request->instrumentalness;
            $song->liveness = is_null($request -> liveness) ? $song->key : $request->liveness;
            $song->valence = is_null($request -> valence) ? $song->valence : $request->valence;
            $song->mood = is_null($request -> mood) ? $song->mood : $request->mood;
            $song->language = is_null($request -> language) ? $song->language : $request->language;
            $song->system_entry_date = is_null($request -> system_entry_date) ? $song->system_entry_date : $request->system_entry_date;
            $song->album_id = is_null($request -> album_id) ? $song->album_id : $request->album_id;
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
        if (Song::where('id', $id) -> exists()){
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