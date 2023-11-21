<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Http\Resources\AlbumResource;

class AlbumController extends Controller
{
    // Common resource routes:
    // index - Show all albums
    // show - Show single album
    // store - Store new album
    // update - Update album
    // destroy - Delete album

    public function index(){
        $albums = Album::all();
        return response()->json($albums);
    }

    public function store(Request $request){
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

    public function search_id($id){
        $album = Album::find($id);
        if(!empty($album)){
            return response()->json($album);
        }
        else{
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

    public function update(Request $request, $id){
        if (album::where('album_id', $id) -> exists()){
            $album = album::find($id);
            $album->name = is_null($request -> name) ? $album->name : $request->name;
            $album->album_type = is_null($request -> album_type) ? $album->album_type : $request->album_type;
            $album->image_url = is_null($request -> image_url) ? $album->image_url : $request->image_url;
            $album->artist_id = is_null($request -> artist_id) ? $album->artist_id : $request->artist_id;
            $album->label = is_null($request -> label) ? $album->label : $request->label;
            $album->copyright = is_null($request -> copyright) ? $album->copyright : $request->copyright;
            $album->release_date = is_null($request -> release_date) ? $album->release_date : $request->release_date;
            $album->total_tracks = is_null($request -> total_tracks) ? $album->total_tracks : $request->total_tracks;
            $album->popularity = is_null($request -> popularity) ? $album->popularity : $request->popularity;
            $album->save();
            return response()->json([
                "message" => "Album Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Album not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (Album::where('album_id', $id) -> exists()){
            $album = Album::find($id);
            $album->delete();
            return response()->json([
                "message" => "Album deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Album not found"
            ], 404);
        }
    }
}