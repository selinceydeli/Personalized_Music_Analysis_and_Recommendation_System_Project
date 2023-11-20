<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Performer;
use Illuminate\Http\Request;
use App\Http\Resources\SongResource;
use Illuminate\Support\Facades\Http;
use App\HTTP\Controllers\PerformerController;

class SongController extends Controller
{
    public function index()
    {
        $query = Song::latest();

        // Check if a genre filter is applied
        $selectedGenre = request('genre');
        if ($selectedGenre) {
        }

        // Check if a search filter is applied
        $searchTerm = request('search');
        if ($searchTerm) {
            $performerNames = Performer::where('name', 'LIKE', "%{$searchTerm}%")->pluck('name')->toArray();

            $query->where(function ($query) use ($searchTerm, $performerNames) {
                $query->where('songs.name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('album', function ($subquery) use ($searchTerm, $performerNames) {
                        $subquery->where('albums.name', 'like', '%' . $searchTerm . '%')
                            ->whereHas('performers', function ($innerSubquery) use ($performerNames) {
                                $innerSubquery->whereIn('performers.name', $performerNames);
                            });
                    });

            });
        }

        $songs = $query->paginate(6);

        $performerIds = $songs->pluck('performers')->flatten()->unique(); // Get unique performer IDs from all songs
        // Remove the extra brackets and extract the IDs as strings
        $performerIds = $performerIds->map(function ($id) {
            // Assuming each ID is wrapped in square brackets and presented as a string
            return trim($id, '[""]');
        });

        $performerController = new PerformerController();
        $performers = [];
        foreach ($performerIds as $id) {

            // Make an HTTP request to fetch performer data
            $response = $performerController->search_id($id); // Directly calling the method

            if ($response->getStatusCode() == 200) { // Checking if performer is found
                $performers[$id] = $response->getData(); // Assuming getData() gets the data from the response
            }
        }

        // Append the genre and search parameters to the pagination links
        $songs->appends([
            'genre' => $selectedGenre,
            'search' => $searchTerm,
        ]);

        return view('songs.index', [
            'songs' => $songs,
            'selectedGenre' => $selectedGenre,
            'performers' => $performers,
        ]);
    }


    public function store(Request $request)
    {
        $song = new Song;
        $song->name = $request->name;
        $song->performers = $request->performers;
        $song->isrc = $request->isrc;
        $song->duration = $request->duration;
        $song->lyrics = $request->lyrics;
        $song->explicit = $request->explicit;
        $song->tempo = $request->tempo;
        $song->key = $request->key;
        $song->system_entry_date = $request->system_entry_date;
        $song->album_id = $request->album_id;
        $song->save();
        return response()->json([
            "message" => "Song added"
        ], 200);
    }

    public function search_id($id)
    {
        $song = Song::find($id);
        if (!empty($song)) {
            return response()->json($song);
        } else {
            return response()->json([
                "message" => "Song not found"
            ], 404);
        }
    }

    public function searchSongs(Request $request)
    {
        $searchQuery = $request->input('search');

        $songs = Song::where('name', 'like', '%' . $searchQuery . '%')->get();

        return view('songs.search-results', compact('songs'));
    }

    /*
    returns all songs that contain the given searchTerm within their name
    */
    public function search_name($searchTerm)
    {
        $songs = Song::where('name', 'LIKE', "%{$searchTerm}%")->get();

        return SongResource::collection($songs);
    }

    public function update(Request $request, $id)
    {
        if (Song::where('song_id', $id)->exists()) {
            $song = Song::find($id);
            $song->name = is_null($request->name) ? $song->name : $request->name;
            $song->lyrics = is_null($request->lyrics) ? $song->lyrics : $request->lyrics;
            $song->isrc = is_null($request->isrc) ? $song->isrc : $request->isrc;
            $song->performers = is_null($request->performers) ? $song->performers : $request->performers;
            $song->tempo = is_null($request->tempo) ? $song->tempo : $request->tempo;
            $song->key = is_null($request->key) ? $song->key : $request->key;
            $song->system_entry_date = is_null($request->system_entry_date) ? $song->system_entry_date : $request->system_entry_date;
            $song->album_id = is_null($request->album_id) ? $song->album_id : $request->album_id;
            $song->explicit = is_null($request->explicit) ? $song->explicit : $request->explicit;
            $song->duration = is_null($request->duration) ? $song->duration : $request->duration;
            $song->save();
            return response()->json([
                "message" => "Song Updated"
            ], 200);
        } else {
            return response()->json([
                "message" => "Song not found!"
            ], 404);
        }
    }

    public function destroy($id)
    {
        if (Song::where('song_id', $id)->exists()) {
            $song = Song::find($id);
            $song->delete();
            return response()->json([
                "message" => "Song deleted"
            ], 200);
        } else {
            return response()->json([
                "message" => "Song not found"
            ], 404);
        }
    }
}
