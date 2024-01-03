<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\User;
use App\Models\Playlist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;


class PlaylistController extends Controller
{

    public function index($id)
    {
        $playlist = Playlist::find($id);
        $songs = $playlist->songs;

        $albums = [];
        foreach ($songs as $song) {
            // Assuming there's a direct relationship between Song and Album models
            $album = $song->album;
        
            if ($album) {
                // Add the album to the $albums array
                $albums[] = $album;
            }
        }
        
        // Now $albums contains the corresponding albums of the songs in the playlist
        
        
        $albumPerformers = [];
        foreach ($albums as $album) {
            $performerController = new PerformerController();
            $response = $performerController->search_id($album->artist_id);

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
                    $performerController = new PerformerController();
                    $response = $performerController->search_id($subId); // Assuming search_id() takes a string parameter

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

        // Iterate through each song ID and retrieve the latest user rating
        foreach ($songs as $s) {
            // Retrieve the latest user rating for the current song
            $songRatingsController = new SongRatingController();
            $latestUserRating = $songRatingsController->getLatestUserRating(auth()->user()->username, $s->song_id);

            // Build the ratings map entry for this song
            $ratingsMap[$s->song_id] = [
                'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
            ];
        }        
        return view('playlists.index', [
            'playlist' => $playlist,
            'songs' => $songs,
            'albums' => $albums,
            'albumPerformers' => $albumPerformers,
            'performersSongs' => $performersSongs,
            'ratingsMap' => $ratingsMap,
        ]);
    }
    public function storeWithUser(Request $request)
    {
        // Validate the request data
        $request->validate([
            'username' => 'required|exists:users,username', // Make sure the user exists
            'playlist_name' => 'required|string|max:255',  // Validate the playlist name
        ]);

        try {
            // Retrieve the user by username
            $user = User::where('username', $request->username)->firstOrFail();

            // Create a new playlist
            $playlist = new Playlist();
            $playlist->playlist_name = $request->playlist_name;
            $playlist->save();

            // Attach the playlist to the user
            $user->playlists()->attach($playlist->id);
            return response()->json([
                "message" => "Playlist created"
            ], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                "message" => "Playlist creation failed"
            ], 404);
        }

        // Return a response, for example a redirect with a success message
        return response()->json([
            "message" => "Playlist created"
        ], 200);
    }

    public function getUserPlaylists(Request $request)
    {
        // Validate the request data
        $request->validate([
            'username' => 'required|string|exists:users,username', // Ensure the username exists.
        ]);

        // Attempt to retrieve the user and load their playlists
        try {
            $user = User::where('username', $request->username)->with('playlists')->firstOrFail();
            return response()->json([
                'success' => true,
                'playlists' => $user->playlists
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }

    public function addSongsToPlaylist(Request $request, $playlistId)
    {
        // Validate the request data
        $request->validate([
            'song_ids' => 'required|array',
            'song_ids.*' => 'required|exists:songs,song_id', // Validate each song ID exists
        ]);

        // Find the playlist and attach the songs
        $playlist = Playlist::findOrFail($playlistId);

        // You can use syncWithoutDetaching to avoid detaching existing songs
        // and to prevent adding duplicates
        $playlist->songs()->syncWithoutDetaching($request->song_ids);

        return response()->json([
            'success' => true,
            'message' => 'Songs added to playlist successfully.',
            'playlist' => $playlist->load('songs') // Load the songs relationship
        ]);
    }

    public function addUsersToPlaylist(Request $request, $playlistId)
    {
        // Validate the request data
        $request->validate([
            'usernames' => 'required|array',
            'usernames.*' => 'required|exists:users,username', // Validate each username exists
        ]);

        // Retrieve the playlist by ID
        $playlist = Playlist::findOrFail($playlistId);

        // Use a transaction to ensure database consistency
        DB::transaction(function () use ($playlist, $request) {
            // Add users to the playlist
            // syncWithoutDetaching ensures existing users remain and no duplicates are added
            $playlist->users()->syncWithoutDetaching($request->usernames);
        });

        return response()->json([
            'success' => true,
            'message' => 'Users added to playlist successfully.',
            'playlist' => $playlist->load('users') // Load the users relationship
        ]);
    }

    public function removeSongFromPlaylist($playlistId, $songId)
    {
        // Find the playlist by ID and remove the song
        $playlist = Playlist::findOrFail($playlistId);

        // Detach the song from the playlist
        $playlist->songs()->detach($songId);

        return response()->json([
            'success' => true,
            'message' => 'Song removed from playlist successfully.',
            'playlist' => $playlist->load('songs') // Optionally, load the songs relationship to show updated list
        ]);
    }

    public function destroy($playlistId)
    {
        try {
            $playlist = Playlist::findOrFail($playlistId);

            // Perform the deletion of the playlist.
            $playlist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Playlist deleted successfully.'
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Playlist not found.'
            ], 404);
        }
    }
}
