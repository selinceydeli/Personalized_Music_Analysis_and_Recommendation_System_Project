<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformerRating;
use App\Http\Resources\PerformerRatingResource;
use Illuminate\Support\Facades\DB;

class PerformerRatingController extends Controller
{
    public function index(){
        $performerratings = PerformerRating::all();
        return response()->json($performerratings);
    }

    public function store(Request $request){
        $performerrating = new PerformerRating;
        $performerrating->rating = $request->input('rating');
        $performerrating->username = auth()->user()->username;
        $performerrating->artist_id = $request->input('artist_id');
        $performerrating->date_rated = now();
        $performerrating->save();
        return redirect('/')->with('message', 'Performer rated successfully');
    }

    public function search_id_performer($id){

        $performerratings = PerformerRating::where('artist_id', '=', "{$id}")->get();

        return PerformerRatingResource::collection($performerratings);
    }

    public function search_id_user($username){

        $performerratings = PerformerRating::where('username', '=', "{$username}")->get();

        return PerformerRatingResource::collection($performerratings);
    }

    public function update(Request $request, $id){
        if (PerformerRating::where('id', $id) -> exists()){
            $performerrating = PerformerRating::find($id);
            $performerrating->rating = is_null($request -> rating) ? $performerrating->rating : $request->rating;
            $performerrating->username = is_null($request -> username) ? $performerrating->username : $request->username;
            $performerrating->artist_id = is_null($request -> artist_id) ? $performerrating->artist_id : $request->artist_id;
            $performerrating->date_rated = is_null($request -> date_rated) ? $performerrating->date_rated : $request->date_rated;
            $performerrating->save();
            return response()->json([
                "message" => "Performer rating Updated"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Performer rating not found!"
            ], 404);
        }
    }

    public function destroy($id){
        if (PerformerRating::where('id', $id) -> exists()){
            $performerrating = PerformerRating::find($id);
            $performerrating->delete();
            return response()->json([
                "message" => "Performer rating deleted"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "Performer rating not found"
            ], 404);
        }
    }

    // Methods defined for analysis functionality
    public function getAverageRatingsForArtists(Request $request)
    {
        // Extract the artist names and months from the request
        $artistNames = $request->input('artistNames');
        $months = $request->input('months', 6); // Default to 6 months if not specified

        // Calculate the start date
        $startDate = now()->subMonths($months);

        // Get average ratings for the specified artists since the start date
        $ratings = PerformerRating::select('performers.name', DB::raw('AVG(performer_ratings.rating) as average_rating'))
                    ->join('performers', 'performer_ratings.artist_id', '=', 'performers.artist_id')
                    ->whereIn('performers.name', $artistNames)
                    ->where('performer_ratings.date_rated', '>=', $startDate)
                    ->groupBy('performers.name')
                    ->get()
                    ->keyBy('name'); // Key the collection by performer name for easy lookup

        // Prepare the results in the order of the inputted artist names
        $orderedRatings = [];
        foreach ($artistNames as $artistName) {
            $orderedRatings[$artistName] = $ratings[$artistName]->average_rating ?? null;
        }

        return $orderedRatings;
    }

    public function averageRatings(Request $request, PerformerRatingController $performerRatingController)
    {
        $artists = $this->getDistinctArtists();

        // You may also need to fetch time spans based on your requirements

        return view('analysis.average_ratings', ['artists' => $artists]);
    }
}