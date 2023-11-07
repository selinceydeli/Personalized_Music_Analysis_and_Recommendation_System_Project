<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Http\Resources\AlbumResource;
use Illuminate\Support\Facades\DB;

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

    // Methods defined for analysis functionality
    public function topRatedAlbumsByEra($username, $era)
    {
        // Define the start and end years based on the era
        switch ($era) {
            case '50s':
                $yearsRange = ['1950-01-01', '1959-12-31'];
                break;
            case '60s':
                $yearsRange = ['1960-01-01', '1969-12-31'];
                break;
            case '70s':
                $yearsRange = ['1970-01-01', '1979-12-31'];
                break;
            case '80s':
                $yearsRange = ['1980-01-01', '1989-12-31'];
                break;
            case '90s':
                $yearsRange = ['1990-01-01', '1999-12-31'];
                break;
            case '20s':
                $yearsRange = ['2000-01-01', '2029-12-31'];
                break;
            default:
                // Handle invalid era input or set a default range
                return response()->json(['error' => 'Invalid era provided.'], 400);
        }

        // Create a subquery for the average rating of albums by the user
        $ratingSubQuery = DB::table('album_ratings')
            ->select('album_id', DB::raw('AVG(rating) as average_rating'))
            ->where('username', $username)
            ->groupBy('album_id');

        // Join the tables and select the top 10 albums from the specified era
        $topAlbums = DB::table('albums')
            ->joinSub($ratingSubQuery, 'rating', function ($join) {
                $join->on('albums.id', '=', 'rating.album_id');
            })
            ->join('songs', 'albums.id', '=', 'songs.album_id')
            ->select('albums.name', 'albums.image_url', 'rating.average_rating')
            ->whereBetween('songs.publ_date', $yearsRange)
            ->groupBy('albums.id', 'albums.name', 'albums.image_url')
            ->orderBy('average_rating', 'DESC')
            ->take(10)
            ->get();

        return response()->json($topAlbums);
    }
}