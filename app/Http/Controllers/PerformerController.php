<?php

namespace App\Http\Controllers;

use PDO;
use App\Models\Song;
use App\Models\Album;
use App\Models\Performer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $songs = Song::whereJsonContains('performers', $performerId)
        ->leftJoin('song_ratings', 'songs.song_id', '=', 'song_ratings.song_id')
        ->select('songs.*', DB::raw('IFNULL(AVG(song_ratings.rating), 0) as average_song_rating'))
        ->groupBy('songs.song_id')
        ->orderByDesc('average_song_rating')
        ->paginate(10);
    
        $performer = Performer::where('performers.artist_id', $performerId)
        ->leftJoin('performer_ratings', 'performers.artist_id', '=', 'performer_ratings.artist_id')
        ->select('performers.*', DB::raw('IFNULL(AVG(performer_ratings.rating), 0) as average_performer_rating'))
        ->groupBy('performers.artist_id')
        ->orderByDesc('average_performer_rating')
        ->first();

        $albums = Album::where('albums.artist_id', $performerId)
        ->leftJoin('album_ratings', 'albums.album_id', '=', 'album_ratings.album_id')
        ->select('albums.*', DB::raw('IFNULL(AVG(album_ratings.rating), 0) as average_album_rating'))
        ->groupBy('albums.album_id')
        ->orderByDesc('average_album_rating')
        ->get();
        
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

        $songPerformers = $songs->map(function ($song) {
            return is_array($song) ? $song : json_decode($song, true);
        })->pluck('performers')->map(function ($performers) {
            return is_array($performers) ? $performers : json_decode($performers, true);
        });


        $performersSongs = [];
        foreach ($songPerformers as $key => $id) {
            if (is_array($id)) {
                foreach ($id as $subId) {
                    // Make sure $subId is a string without quotes
                    $subId = trim($subId, '"');

                    // Make an HTTP request to fetch performer data for each subId
                    $response = $this->search_id($subId); // Assuming search_id() takes a string parameter

                    if ($response->getStatusCode() == 200) { // Checking if performer is found
                        $performersSongs[$key][$subId] = $response->getData(); // Assuming getData() gets the data from the response
                    }
                }
            } else {
                // Make an HTTP request to fetch performer data for $id
                $id = trim($id, '"');

                $response = $this->search_id($id); // Assuming search_id() takes a string parameter

                if ($response->getStatusCode() == 200) { // Checking if performer is found
                    $performersSongs[$key][$id] = $response->getData(); // Assuming getData() gets the data from the response
                }
            }
        }

        // Initialize an empty array to store the mapping
        $ratingsMap = [];

        if (auth()->check()) {
            $username = auth()->user()->username;

            // Iterate through each song ID and retrieve the latest user rating
            foreach ($songs as $s) {
                // Retrieve the latest user rating for the current song
                $songRatingsController = new SongRatingController();
                $latestUserRating = $songRatingsController->getLatestUserRating($username, $s->song_id);

                // Build the ratings map entry for this song
                $ratingsMap[$s->song_id] = [
                    'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
                ];
            }
            $latestPerformerRating=$songRatingsController->getLatestUserRatingForPerformer($username, $performerId);
            $latestPerformerRating = $latestPerformerRating ? $latestPerformerRating->rating : null;
            return view('performers.show', [
                'performer' => $performer,
                'albumPerformers' => $albumPerformers,
                'songs' => $songs,
                'albums' => $albums,
                'songId' => $songId,
                'performersSongs' => $performersSongs,
                'ratingsMap' => $ratingsMap,
                'latestPerformerRating' => $latestPerformerRating,
            ]);
        }

        return view('performers.show', [
            'performer' => $performer,
            'albumPerformers' => $albumPerformers,
            'songs' => $songs,
            'albums' => $albums,
            'songId' => $songId,
            'performersSongs' => $performersSongs,
            'ratingsMap' => $ratingsMap,
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

    public function destroy($id){
        if (Performer::where('artist_id', $id) -> exists()){
            $performer = Performer::where('artist_id',$id);

            if(Album::where('artist_id', $id) -> exists()){
                Album::where('artist_id', $id)->delete();
            }

            $performer->delete();
            return response()->json([
                "message" => "Performer and his/her songs and albums are deleted"
            ], 200);
        } else {
            return response()->json([
                "message" => "Performer not found"
            ], 404);
        }
    }
}
