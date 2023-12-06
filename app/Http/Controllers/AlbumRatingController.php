<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlbumRating;
use App\Http\Resources\AlbumRatingResource;
use Illuminate\Support\Facades\DB;

class AlbumRatingController extends Controller
{
    public function index(){
        $albumratings = AlbumRating::all();
        return response()->json($albumratings);
    }

    public function store(Request $request){
        $albumrating = new AlbumRating;
        $albumrating->rating = $request->rating;
        $albumrating->username = $request->username;
        $albumrating->album_id = $request->album_id;
        $albumrating->date_rated = $request->date_rated;
        $albumrating->save();
        return response()->json([
            "message" => "Album rating added"
        ], 200);
    }

    public function search_id_album($id){

        $albumratings = AlbumRating::where('album_id', '=', "{$id}")->get();

        return AlbumRatingResource::collection($albumratings);
    }

    public function search_id_user($username){

        $albumratings = AlbumRating::where('username', '=', "{$username}")->get();

        return AlbumRatingResource::collection($albumratings);
    }

    public function update(Request $request, $id){
        if (AlbumRating::where('id', $id) -> exists()){
            $albumrating = AlbumRating::find($id);
            $albumrating->rating = is_null($request -> rating) ? $albumrating->rating : $request->rating;
            $albumrating->username = is_null($request -> username) ? $albumrating->username : $request->username;
            $albumrating->album_id = is_null($request -> album_id) ? $albumrating->album_id : $request->album_id;
            $albumrating->date_rated = is_null($request -> date_rated) ? $albumrating->date_rated : $request->date_rated;
            $albumrating->save();
            return response()->json([
                "message" => "Album rating Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Album rating not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (AlbumRating::where('id', $id) -> exists()){
            $albumrating = AlbumRating::find($id);
            $albumrating->delete();
            return response()->json([
                "message" => "Album rating deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Album rating not found"
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
                $join->on('albums.album_id', '=', 'rating.album_id');
            })
            ->join('songs', 'albums.album_id', '=', 'songs.album_id')
            ->select('albums.name', 'albums.image_url', 'rating.average_rating')
            ->whereBetween('albums.release_date', $yearsRange)
            ->groupBy('albums.album_id', 'albums.name', 'albums.image_url')
            ->orderBy('average_rating', 'DESC')
            ->take(10)
            ->get();

        return response()->json($topAlbums);
        // Return the view with the topAlbums data
        return view('analysis.favorite_albums', compact('topAlbums', 'eras'));

    }
}