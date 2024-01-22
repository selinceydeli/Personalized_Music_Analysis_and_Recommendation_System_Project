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
        $q = Song::leftJoin('song_ratings', 'songs.song_id', '=', 'song_ratings.song_id')
            ->select('songs.*', DB::raw('IFNULL(AVG(song_ratings.rating), 0) as average_rating'))
            ->groupBy('songs.song_id')
            ->orderByDesc('average_rating')
            ->whereIn('songs.song_id', $playlist->songs->pluck('song_id')->toArray());

        $searchTerm = request('searchplaylistsong');
        if ($searchTerm) {
            $q->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            });
        }

        $songs = $q->paginate(6);

        $user=auth()->user();


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
            'user' => $user,
            'playlist' => $playlist,
            'songs' => $songs,
            'albums' => $albums,
            'albumPerformers' => $albumPerformers,
            'performersSongs' => $performersSongs,
            'ratingsMap' => $ratingsMap,
        ]);
    }
    public function create(Request $request)
    {

        // Validate the request data
        $request->validate([
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
            return redirect("/playlist/{$playlist->id}")->with('message', 'Playlist created');
        } catch (ModelNotFoundException $exception) {
            return redirect()->back()->with('message', 'Failed to create playlist');
        }

        // Return a response, for example a redirect with a success message
        return redirect("/playlist/{$playlist->id}")->with('message', 'Playlist created');
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

    public function add($id)
    {
        $playlist = Playlist::find($id);

        $query = Song::leftJoin('song_ratings', 'songs.song_id', '=', 'song_ratings.song_id')
            ->select('songs.*', DB::raw('IFNULL(AVG(song_ratings.rating), 0) as average_rating'))
            ->groupBy('songs.song_id')
            ->orderByDesc('average_rating')
            ->whereNotIn('songs.song_id', $playlist->songs->pluck('song_id')->toArray());



        // Check if a search filter is applied
        $searchTerm = request('searchaddsong');
        $searchController = new SearchController();
        if ($searchTerm) {
            $performerResults = $searchController->search_performer($searchTerm)->pluck('artist_id')->toArray();

            $query->where(function ($query) use ($searchTerm, $performerResults) {
                $query->where('songs.name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('album', function ($subquery) use ($searchTerm) {
                        $subquery->where('albums.name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhere(function ($subquery) use ($performerResults) {
                        foreach ($performerResults as $artistId) {
                            $subquery->orWhereJsonContains('performers', $artistId);
                        }
                    });
            });
        }

        $songs = $query->paginate(10);

        // Extract the song_id values from the paginated songs
        $songIds = $songs->pluck('song_id')->toArray();
        // Initialize an empty array to store the mapping
        $ratingsMap = [];

        if (auth()->check()) {
            $username = auth()->user()->username;

            // Iterate through each song ID and retrieve the latest user rating
            $songRatingsController = new SongRatingController();
            foreach ($songIds as $songId) {
                // Retrieve the latest user rating for the current song
                $latestUserRating = $songRatingsController->getLatestUserRating($username, $songId);

                // Build the ratings map entry for this song
                $ratingsMap[$songId] = [
                    'latest_user_rating' => $latestUserRating ? $latestUserRating->rating : null,
                ];
            }
        }

        $performerIds = $songs->pluck('performers')->flatten(); // Get unique performer IDs from all songs

        // Remove the extra brackets and extract the IDs as strings
        foreach ($performerIds as $key => $ids) {
            if (is_string($ids) && strpos($ids, ',') !== false) {
                $performerIds[$key] = array_map('trim', explode(',', trim($ids, '[]')));
            } else {
                $performerIds[$key] = trim($ids, '[]');
            }
        }

        $performerController = new PerformerController();
        $performers = [];
        foreach ($performerIds as $key => $id) {
            if (is_array($id)) {
                foreach ($id as $subId) {
                    // Make sure $subId is a string without quotes
                    $subId = trim($subId, '"');

                    // Make an HTTP request to fetch performer data for each subId
                    $response = $performerController->search_id($subId); // Assuming search_id() takes a string parameter

                    if ($response->getStatusCode() == 200) { // Checking if performer is found
                        $performers[$key][$subId] = $response->getData(); // Assuming getData() gets the data from the response
                    }
                }
            } else {
                // Make an HTTP request to fetch performer data for $id
                $id = trim($id, '"');

                $response = $performerController->search_id($id); // Assuming search_id() takes a string parameter

                if ($response->getStatusCode() == 200) { // Checking if performer is found
                    $performers[$key][$id] = $response->getData(); // Assuming getData() gets the data from the response
                }
            }
        }

        // Append the genre and search parameters to the pagination links
        $songs->appends([
            'search' => $searchTerm,
        ]);

        return view('playlists.songs', [
            'songs' => $songs,
            'performers' => $performers,
            'ratingsMap' => $ratingsMap,
            'playlist' => $playlist,
        ]);
    }

    public function addsongs($playlistId, $songId)
    {

        $song = Song::find($songId);

        // Find the playlist and attach the songs
        $playlist = Playlist::findOrFail($playlistId);

        // You can use syncWithoutDetaching to avoid detaching existing songs
        // and to prevent adding duplicates
        $playlist->songs()->syncWithoutDetaching([$song->song_id]);

        $playlist->load('songs');

        return redirect("/playlist/{$playlist->id}")->with('message', 'Song added to playlist successfully.');
    }

    public function adduser($playlistId)
    {
        $playlist = Playlist::findOrFail($playlistId);

        $user = User::where('username', auth()->user()->username)->firstOrFail();

        // Get the friends of the user
        $friends = $user->friends;

        // Filter friends who are not collaborating on the given playlist
        $notCollaboratingFriends = $friends->filter(function ($friend) use ($playlist) {
            return !$playlist->users->contains($friend);
        });

        // Convert the collection to a query builder instance
        $notCollaboratingFriendsQuery = User::whereIn('username', $notCollaboratingFriends->pluck('username'))->withCount('friendsOfMine')
        ->orderByDesc('friends_of_mine_count'); // Retrieve the results;

        $searchTerm = request('searchusers');
        if ($searchTerm) {
            $notCollaboratingFriendsQuery->where(function ($query) use ($searchTerm) {
                $query->where('username', 'like', '%' . $searchTerm . '%');
            });
        }

        // Paginate the query by 10 items per page
        $paginatedFriends = $notCollaboratingFriendsQuery->paginate(10);

        return view('playlists.users', [
            'playlist' => $playlist,
            'users' => $paginatedFriends,
        ]);
    }


    public function addusers($playlistId, $username)
    {
        // Retrieve the playlist by ID
        $playlist = Playlist::findOrFail($playlistId);

        // Use a transaction to ensure database consistency
        DB::transaction(function () use ($playlist, $username) {
            // Add users to the playlist
            // syncWithoutDetaching ensures existing users remain and no duplicates are added
            $playlist->users()->syncWithoutDetaching($username);
        });

        $playlist->load('users'); // Load the users relationship

        return redirect("/playlist/{$playlist->id}")->with('message', 'User added to playlist successfully.');
    }

    public function remove($playlistId, $songId)
    {
        // Find the playlist by ID and remove the song
        $playlist = Playlist::findOrFail($playlistId);

        // Detach the song from the playlist
        $playlist->songs()->detach($songId);

        return redirect()->back()->with('message', 'Song removed from playlist');
    }

    public function destroy($playlistId)
    {
        try {
            $playlist = Playlist::findOrFail($playlistId);

            // Perform the deletion of the playlist.
            $playlist->delete();

            return redirect()->back()->with('message', 'Playlist deleted');
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Playlist not found.'
            ], 404);
        }
    }
}
