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
        $album = new Album;
        $album->name = $request->name;
        $album->is_single = $request->is_single;
        $album->image_url = $request->image_url;
        $album->save();
        return response()->json([
            "message" => "Album added"
        ], 201); // In the context of RESTful API design, when you create a new resource, 
                // the expected status code is 201 Created.
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
        if (album::where('id', $id) -> exists()){
            $album = album::find($id);
            $album->name = is_null($request -> name) ? $album->name : $request->name;
            $album->is_single = is_null($request -> is_single) ? $album->is_single : $request->is_single;
            $album->image_url = is_null($request -> image_url) ? $album->image_url : $request->image_url;
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
        if (Album::where('id', $id) -> exists()){
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
