<?php

namespace App\Http\Controllers;

use PDO;
use App\Models\Song;
use App\Models\Album;
use App\Models\Performer;
use Illuminate\Http\Request;
use App\Http\Resources\PerformerResource;

class PerformerController extends Controller
{
    public function index()
    {
        $performers = Performer::all();
        return response()->json($performers);
    }
    public function show($performerId, Request $request)
    {

        $songId = $request->input('song-id');

        $songs = Song::whereJsonContains('performers', $performerId)->paginate(10);

        $performer = Performer::where('artist_id', $performerId)->orderBy('name')->first();

        $albums = Album::where('artist_id', $performer->artist_id)->get();

        $albumPerformers = [];
        foreach ($albums as $album) {
            $response = $this->search_id($album->artist_id);

            if ($response->getStatusCode() == 200) {
                $performers = $response->getData(); // Assuming getData() gets the data from the response

                // Check if album ID exists in $albumPerformers array
                if (!isset($albumPerformers[$album->album_id])) {
                    $albumPerformers[$album->album_id] = [];
                }

                // Append performers to the album's array
                $albumPerformers[$album->album_id][] = $performers;
            }
        }

        return view('performers.show', [
            'performer' => $performer,
            'albumPerformers' => $albumPerformers,
            'songs' => $songs,
            'albums' => $albums,
            'songId' => $songId,
        ]);
    }

    public function store(Request $request){
        // Define the attributes you want to check for uniqueness.
        $uniqueAttributes = [
            'artist_id' => $request->album_id
        ];

        // Additional data that should be included if a new album is being created.
        $additionalData = [
            'name' => $request->name,
            'genre' => $request->genre,
            'image_url' => $request->image_url,
            'popularity' => $request->popularity
        ];

        // Use firstOrCreate to either find the existing album or create a new one.
        $performer = Performer::firstOrCreate($uniqueAttributes, $additionalData);

        if ($performer->wasRecentlyCreated) {
            // Album was created
            return response()->json([
                "message" => "Performer added"
            ], 201); // HTTP status code 201 means "Created"
        } else {
            // Album already exists
            return response()->json([
                "message" => "Performer already exists"
            ], 200); // HTTP status code 200 means "OK"
        }
    }

    public function search_id($id){
        $performer = Performer::where('artist_id', $id)->first();    
        if ($performer) {
            return response()->json($performer);
        } else {
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

    public function update(Request $request, $id)
    {
        if (Performer::where('artist_id', $id)->exists()) {
            $performer = Performer::find($id);
            $performer->name = is_null($request->name) ? $performer->name : $request->name;
            $performer->genre = is_null($request->genre) ? $performer->genre : $request->genre;
            $performer->popularity = is_null($request->popularity) ? $performer->popularity : $request->popularity;
            $performer->image_url = is_null($request->image_url) ? $performer->image_url : $request->image_url;
            $performer->save();
            return response()->json([
                "message" => "Performer Updated"
            ], 200);
        } else {
            return response()->json([
                "message" => "performer not found!"
            ], 404);
        }
    }

    public function destroy($id)
    {
        if (Performer::where('artist_id', $id)->exists()) {
            $performer = Performer::find($id);
            $performer->delete();
            return response()->json([
                "message" => "Performer deleted"
            ], 200);
        } else {
            return response()->json([
                "message" => "Performer not found"
            ], 404);
        }
    }
}
