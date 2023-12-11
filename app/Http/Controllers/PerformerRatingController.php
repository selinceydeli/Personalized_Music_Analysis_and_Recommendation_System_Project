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
        return back()->with('message', 'Performer rated successfully');
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

    public function searchArtists(Request $request)
    {
        $query = $request->input('query');

        // Perform a case-insensitive search for artist names that match the query
        $artists = PerformerRating::where('name', 'ilike', '%' . $query . '%')->pluck('name');

        return response()->json($artists);
    }

    // Methods defined for analysis functionality
    public function getAverageRatingsForArtists(Request $request)
    {
        
        $artistNamesString = $request->input('artistNames', ''); // Default to an empty array if not specified
        $artistNames = explode(',', $artistNamesString);
        $artistNames = array_map('trim', $artistNames);

        $months =  6; // Default to 6 months 

        // Calculate the start date
        $startDate = now()->subMonths($months);

        // Ensure $artistNames is an array
        $artistNames = is_array($artistNames) ? $artistNames : [];

        if (!empty($artistNames)) {
            
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
            //dd($orderedRatings);
            return view('analysis.average_ratings', [
                'artistNames' => $artistNames,
                'orderedRatings' => $orderedRatings
            ]);        
        }

        // Return an empty array if no artist names are provided
        return view('analysis.average_ratings', ['artistNames' => $artistNames, 'orderedRatings' => []]);
    }
}
