<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use App\Models\Performer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AlbumResource;

class AlbumController extends Controller
{
    // Common resource routes:
    // index - Show all albums
    // show - Show single album
    // store - Store new album
    // update - Update album
    // destroy - Delete album

    public function index()
    {
        $albums = Album::all();
        return response()->json($albums);
    }

    // Show single album
    public function show(Album $album, Request $request)
    {

        $songId = $request->input('song-id');

        $song = Song::where('song_id', $songId)->first();

        // Get the album's average rating separately
        $albumAverageRating = Album::where('albums.album_id', $song->album->album_id)
            ->leftJoin('album_ratings', 'albums.album_id', '=', 'album_ratings.album_id')
            ->select(
                'albums.*',
                DB::raw('IFNULL(AVG(album_ratings.rating), 0) as average_album_rating')
            )
            ->groupBy('albums.album_id')
            ->value('average_album_rating');

        // Retrieve all songs with the same album_id as $song->album->id
        $songsWithSameAlbum = Song::where('album_id', $song->album->album_id)
            ->leftJoin('song_ratings', 'songs.song_id', '=', 'song_ratings.song_id')
            ->select('songs.*', DB::raw('IFNULL(AVG(song_ratings.rating), 0) as average_rating'))
            ->groupBy('songs.song_id')
            ->orderByDesc('average_rating')
            ->get();

        $songIds = $songsWithSameAlbum->pluck('song_id')->toArray();
        // Initialize an empty array to store the mapping
        $ratingsMap = [];

        if (auth()->check()) {
            $username = auth()->user()->username;

            // Iterate through each song ID and retrieve the latest user rating
            foreach ($songIds as $s) {
                // Retrieve the latest user rating for the current song
                $songRatingsController = new SongRatingController();
                $latestUserRating = $songRatingsController->getLatestUserRating($username, $s);

                // Build the ratings map entry for this song
                $ratingsMap[$s] = [
                    'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
                ];
            }
            $latestAlbumRating=$songRatingsController->getLatestUserRatingForAlbum($username, $song->album->album_id);
            $latestAlbumRating = $latestAlbumRating ? $latestAlbumRating->rating : null;
        }

        // Access the performer IDs from the song
        $performersIds = $song->performers; // JSON field from the Song model


        // Assuming $performerIds contains the performer IDs associated with the song
        $performers = Performer::whereIn('artist_id', $performersIds)->orderBy('name')->get();

        // Collect all genres of the performers
        $allGenres = [];
        foreach ($performers as $performer) {
            $performerGenres = json_decode($performer->genre);
            $allGenres = array_merge($allGenres, $performerGenres);
        }

        // Get unique genres by converting the array to a collection and using unique method
        $uniqueGenres = collect($allGenres)->unique();

        // Convert the unique genres collection back to an array
        $uniqueGenresArray = $uniqueGenres->values()->all();

        return view('songs.show', [
            'album' => $album,
            'song' => $song,
            'performers' => $performers,
            'songs' => $songsWithSameAlbum,
            'genres' => $uniqueGenresArray,
            'songId' => $songId,
            'ratingsMap' => $ratingsMap,
            'albumAverageRating' => $albumAverageRating,
            'latestAlbumRating' => $latestAlbumRating,
        ]);
    }

    public function store(Request $request)
    {
        // Define the attributes you want to check for uniqueness.
        $uniqueAttributes = [
            'album_id' => $request->album_id
        ];

        // Additional data that should be included if a new album is being created.
        $additionalData = [
            'name' => $request->name,
            'artist_id' => $request->artist_id,
            'release_date' => $request->release_date,
            'album_type' => $request->album_type,
            'image_url' => $request->image_url,
            'label' => $request->label,
            'copyright' => $request->copyright,
            'total_tracks' => $request->total_tracks,
            'popularity' => $request->popularity
        ];

        // Use firstOrCreate to either find the existing album or create a new one.
        $album = Album::firstOrCreate($uniqueAttributes, $additionalData);

        if ($album->wasRecentlyCreated) {
            // Album was created
            return response()->json([
                "message" => "Album added"
            ], 201); // HTTP status code 201 means "Created"
        } else {
            // Album already exists
            return response()->json([
                "message" => "Album already exists"
            ], 200); // HTTP status code 200 means "OK"
        }
    }

    public function search_id($id)
    {
        $album = Album::find($id);
        if (!empty($album)) {
            return response()->json($album);
        } else {
            return response()->json([
                "message" => "Album not found"
            ], 404);
        }
    }

    public function search_name($searchTerm)
    {
        $albums = Album::where('name', 'LIKE', "%{$searchTerm}%")->get();

        return AlbumResource::collection($albums);
    }

    public function update(Request $request, $id)
    {
        if (album::where('album_id', $id)->exists()) {
            $album = album::find($id);
            $album->name = is_null($request->name) ? $album->name : $request->name;
            $album->album_type = is_null($request->album_type) ? $album->album_type : $request->album_type;
            $album->image_url = is_null($request->image_url) ? $album->image_url : $request->image_url;
            $album->artist_id = is_null($request->artist_id) ? $album->artist_id : $request->artist_id;
            $album->label = is_null($request->label) ? $album->label : $request->label;
            $album->copyright = is_null($request->copyright) ? $album->copyright : $request->copyright;
            $album->release_date = is_null($request->release_date) ? $album->release_date : $request->release_date;
            $album->total_tracks = is_null($request->total_tracks) ? $album->total_tracks : $request->total_tracks;
            $album->popularity = is_null($request->popularity) ? $album->popularity : $request->popularity;
            $album->save();
            return response()->json([
                "message" => "Album Updated"
            ], 200);
        } else {
            return response()->json([
                "message" => "Album not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (Album::where('album_id', $id) -> exists()){
            $album = Album::where('album_id',$id);
            $album->delete();
            return response()->json([
                "message" => "Album deleted"
            ], 200);
        } else {
            return response()->json([
                "message" => "Album not found"
            ], 404);
        }
    }
}
