<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Performer;
use App\Models\SongRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SongResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\PerformerController;

function flattenArray($array)
{
    $result = [];
    array_walk_recursive($array, function ($value) use (&$result) {
        $result[] = $value;
    });
    return $result;
}

class SongController extends Controller
{
    public function index()
    {

        $query = Song::leftJoin('song_ratings', 'songs.song_id', '=', 'song_ratings.song_id')
            ->select('songs.*', DB::raw('IFNULL(AVG(song_ratings.rating), 0) as average_rating'))
            ->groupBy('songs.song_id')
            ->orderByDesc('average_rating');

        // Check if a genre filter is applied
        $selectedGenre = request('genre');
        if ($selectedGenre) {
            $genres = $this->getSongsByGenre($selectedGenre)->getData();

            // Extract song IDs from the array response
            $songIds = collect($genres)->pluck('song_id')->toArray();

            // Update the query with the song IDs from the selected genre
            $query->whereIn('songs.song_id', $songIds);
        }

        // Check if a search filter is applied
        $searchTerm = request('search');
        $searchController = new SearchController();
        if ($searchTerm) {
            $performerResults = $searchController->search_performer($searchTerm)->pluck('artist_id')->toArray();

            $query->where(function ($query) use ($searchTerm, $performerResults) {
                $query->where('songs.name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('album', function ($subquery) use ($searchTerm) {
                        $subquery->where('albums.name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhere(function ($subquery) use ($performerResults) {
                        foreach ($performerResults as $artistId) {
                            $subquery->orWhereJsonContains('performers', $artistId);
                        }
                    });
            });
        }

        $songs = $query->paginate(6);

        // Extract the song_id values from the paginated songs
        $songIds = $songs->pluck('song_id')->toArray();
        // Initialize an empty array to store the mapping
        $ratingsMap = [];

        if (auth()->check()) {
            $username = auth()->user()->username;

            // Iterate through each song ID and retrieve the latest user rating
            $songRatingsController = new SongRatingController();
            foreach ($songIds as $songId) {
                // Retrieve the latest user rating for the current song
                $latestUserRating = $songRatingsController->getLatestUserRating($username, $songId);

                // Build the ratings map entry for this song
                $ratingsMap[$songId] = [
                    'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
                ];
            }
        }

        $performerIds = $songs->pluck('performers')->flatten(); // Get unique performer IDs from all songs

        // Remove the extra brackets and extract the IDs as strings
        foreach ($performerIds as $key => $ids) {
            if (is_string($ids) && strpos($ids, ',') !== false) {
                $performerIds[$key] = array_map('trim', explode(',', trim($ids, '[]')));
            } else {
                $performerIds[$key] = trim($ids, '[]');
            }
        }

        $performerController = new PerformerController();
        $performers = [];
        foreach ($performerIds as $key => $id) {
            if (is_array($id)) {
                foreach ($id as $subId) {
                    // Make sure $subId is a string without quotes
                    $subId = trim($subId, '"');

                    // Make an HTTP request to fetch performer data for each subId
                    $response = $performerController->search_id($subId); // Assuming search_id() takes a string parameter

                    if ($response->getStatusCode() == 200) { // Checking if performer is found
                        $performers[$key][$subId] = $response->getData(); // Assuming getData() gets the data from the response
                    }
                }
            } else {
                // Make an HTTP request to fetch performer data for $id
                $id = trim($id, '"');

                $response = $performerController->search_id($id); // Assuming search_id() takes a string parameter

                if ($response->getStatusCode() == 200) { // Checking if performer is found
                    $performers[$key][$id] = $response->getData(); // Assuming getData() gets the data from the response
                }
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
            'ratingsMap' => $ratingsMap,
        ]);
    }

    public function show($id)
    {
        $song = Song::leftJoin('song_ratings', 'songs.song_id', '=', 'song_ratings.song_id')
            ->select('songs.*', DB::raw('IFNULL(AVG(song_ratings.rating), 0) as average_rating'))
            ->where('songs.song_id', $id) // Filter for the specific song ID
            ->groupBy('songs.song_id')
            ->first(); // Fetch a single result

        // Extract the song_id values from the paginated songs
        $songId = $song->song_id;
        // Initialize an empty array to store the mapping
        $ratingsMap = [];

        if (auth()->check()) {
            $username = auth()->user()->username;

            // Iterate through each song ID and retrieve the latest user rating
            $songRatingsController = new SongRatingController();
            // Retrieve the latest user rating for the current song
            $latestUserRating = $songRatingsController->getLatestUserRating($username, $songId);

            // Build the ratings map entry for this song
            $ratingsMap[$songId] = [
                'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
            ];
        }

        $performerIds = $song->performers;

        // Remove the extra brackets and extract the IDs as strings
        foreach ($performerIds as $key => $ids) {
            if (is_string($ids) && strpos($ids, ',') !== false) {
                $performerIds[$key] = array_map('trim', explode(',', trim($ids, '[]')));
            } else {
                $performerIds[$key] = trim($ids, '[]');
            }
        }

        $performerController = new PerformerController();
        $performers = [];
        foreach ($performerIds as $key => $id) {
            if (is_array($id)) {
                foreach ($id as $subId) {
                    // Make sure $subId is a string without quotes
                    $subId = trim($subId, '"');

                    // Make an HTTP request to fetch performer data for each subId
                    $response = $performerController->search_id($subId); // Assuming search_id() takes a string parameter

                    if ($response->getStatusCode() == 200) { // Checking if performer is found
                        $performers[$key][$subId] = $response->getData(); // Assuming getData() gets the data from the response
                    }
                }
            } else {
                // Make an HTTP request to fetch performer data for $id
                $id = trim($id, '"');

                $response = $performerController->search_id($id); // Assuming search_id() takes a string parameter

                if ($response->getStatusCode() == 200) { // Checking if performer is found
                    $performers[$key][$id] = $response->getData(); // Assuming getData() gets the data from the response
                }
            }
        }

        $relatedSongs = Song::whereJsonContains('performers', $performerIds)
            ->where('songs.song_id', '!=', $id) // Exclude the current song
            ->take(15)
            ->get();

        return view('songs.showSingle', [
            'song' => $song,
            'ratingsMap' => $ratingsMap,
            'performers' => $performers,
            'relatedSongs' => $relatedSongs
        ]);
    }

    public function store(Request $request)
    {
        $uniqueAttributes = [
            'song_id' => $request->song_id
        ];

        $additionalData = [
            'name' => $request->name,
            'performers' => $request->performers,
            'isrc' => $request->isrc,
            'duration' => $request->duration,
            'lyrics' => $request->lyrics,
            'explicit' => $request->explicit,
            'tempo' => $request->tempo,
            'key' => $request->key,
            'mode' => $request->mode,
            'system_entry_date' => $request->system_entry_date,
            'album_id' => $request->album_id,
            'danceability' => $request->danceability,
            'energy' => $request->energy,
            'loudness' => $request->loudness,
            'speechiness' => $request->speechiness,
            'instrumentalness' => $request->instrumentalness,
            'liveness' => $request->liveness,
            'valence' => $request->valence,
            'time_signature' => $request->time_signature,
            'staff' => $request->staff
        ];

        // Use firstOrCreate to either find the existing album or create a new one.
        $song = Song::firstOrCreate($uniqueAttributes, $additionalData);

        if ($song->wasRecentlyCreated) {
            // Album was created
            return response()->json([
                "message" => "Song added"
            ], 201); // HTTP status code 201 means "Created"
        } else {
            // Album already exists
            return response()->json([
                "message" => "Song already exists"
            ], 200); // HTTP status code 200 means "OK"
        }
    }

    public function add()
    {
        return view('songs.manage');
    }

    public function search_id($id)
    {
        $song = Song::where('song_id', $id)->first();
        if ($song) {
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
            $song->mode = is_null($request->mode) ? $song->mode : $request->mode;
            $song->system_entry_date = is_null($request->system_entry_date) ? $song->system_entry_date : $request->system_entry_date;
            $song->album_id = is_null($request->album_id) ? $song->album_id : $request->album_id;
            $song->explicit = is_null($request->explicit) ? $song->explicit : $request->explicit;
            $song->duration = is_null($request->duration) ? $song->duration : $request->duration;
            $song->danceability = is_null($request->danceability) ? $song->danceability : $request->danceability;
            $song->energy = is_null($request->energy) ? $song->energy : $request->energy;
            $song->loudness = is_null($request->loudness) ? $song->loudness : $request->loudness;
            $song->speechiness = is_null($request->speechiness) ? $song->speechiness : $request->speechiness;
            $song->instrumentalness = is_null($request->instrumentalness) ? $song->instrumentalness : $request->instrumentalness;
            $song->liveness = is_null($request->liveness) ? $song->liveness : $request->liveness;
            $song->time_signature = is_null($request->time_signature) ? $song->time_signature : $request->time_signature;
            $song->valence = is_null($request->valence) ? $song->valence : $request->valence;
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
            $name = $song->name;
            $song->delete();
            return redirect('/')->with('message', 'Song ' . $name . ' deleted successfully');
        } else {
            return response()->json([
                "message" => "Song not found"
            ], 404);
        }
    }

    public function getSongsByGenre($genre)
    {
        $jsonGenre = json_encode($genre); // Ensure the genre is in JSON format

        $songsWithGenres = DB::table('songs')
            ->join('performers', function ($join) use ($jsonGenre) {
                $join->whereRaw("json_contains(songs.performers, json_quote(performers.artist_id))")
                    ->whereRaw("json_contains(performers.genre, ?)", [$jsonGenre]);
            })
            ->select('songs.*', 'performers.genre as performer_genres')
            ->get();

        return response()->json($songsWithGenres); // Returns all the fields from the songs table 
        // and an additional field performer_genres for each song
    }

    public function getSongsQuery($genre = null, $searchTerm = null)
    {
        $query = Song::leftJoin('song_ratings', 'songs.song_id', '=', 'song_ratings.song_id')
            ->select('songs.*', DB::raw('IFNULL(AVG(song_ratings.rating), 0) as average_rating'))
            ->groupBy('songs.song_id')
            ->orderByDesc('average_rating');

        if ($genre) {
            // Assuming getSongsByGenre() is another method in your controller
            $songIds = $this->getSongsByGenre($genre)->pluck('song_id')->toArray();
            $query->whereIn('songs.song_id', $songIds);
        }

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('songs.name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('album', function ($subquery) use ($searchTerm) {
                        $subquery->where('albums.name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        return $query;
    }

    //Methods defined for downloading songs rated by the user
    public function getSongsRatedByUser($username)
    {
        $songs = SongRating::where('username', $username)
            ->with('song')  // Load the song relationship
            ->get()
            ->map(function ($rating) {
                return $rating->song;  // Return only the song part
            });

        return response()->json($songs);
    }

    public function downloadAllRatedSongs()
    {
        $username = auth()->user()->username; // Get the authenticated user's username
        $songs = SongRating::where('username', $username)
            ->with('song')
            ->get()
            ->map(function ($rating) {
                return $rating->song;
            });

        $jsonData = json_encode($songs, JSON_PRETTY_PRINT);
        $filename = "all-rated-songs.json";

        return response($jsonData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename={$filename}"
        ]);
    }
}
