<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use App\Models\Performer;
use Illuminate\Http\Request;
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

        // Retrieve all songs with the same album_id as $song->album->id
        $songsWithSameAlbum = Song::where('album_id', $song->album->album_id)->get();

        // Access the performer IDs from the song
        $performersJson = $song->performers; // JSON field from the Song model

        // Retrieve performer IDs from JSON data
        $performerIds = json_decode($performersJson);

        // Assuming $performerIds contains the performer IDs associated with the song
        $performers = Performer::whereIn('artist_id', $performerIds)->orderBy('name')->get();

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
        ]);
    }

    public function store(Request $request)
    {
        $album = new Album;
        $album->name = $request->name;
        $album->album_type = $request->album_type;
        $album->image_url = $request->image_url;
        $album->artist_id = $request->artist_id;
        $album->label = $request->label;
        $album->copyright = $request->copyright;
        $album->release_date = $request->release_date;
        $album->total_tracks = $request->total_tracks;
        $album->popularity = $request->popularity;
        $album->save();
        return response()->json([
            "message" => "Album added"
        ], 201); // In the context of RESTful API design, when you create a new resource, 
        // the expected status code is 201 Created.
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

    public function destroy($id)
    {
        if (Album::where('album_id', $id)->exists()) {
            $album = Album::find($id);
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
