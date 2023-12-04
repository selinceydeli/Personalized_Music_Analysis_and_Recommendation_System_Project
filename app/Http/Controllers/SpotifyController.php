<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SpotifyController extends Controller
{
    public function importSong(Request $request)
    {
        $url = $request->input('spotifyLink');

        if ($this->isSpotifyLink($url)) {
            // Execute the Python script and capture the output, including any errors
            $command = "python tempFunctions/importSongWithLink.py " . escapeshellarg($url) . " 2>&1";
            $result = shell_exec($command);

            if ($result === null) {
                return response()->json([
                    'message' => 'Python is not found. Please install Python on the server.',
                ], 500);
            }

            // Store song information in the session flash data
            //Session::flash('song_info', $result);

            // Redirect the user to the root URL ("/") with a success message
            return redirect('/')->with('success', 'Song information uploaded successfully!');
        } else {
            return response()->json(['message' => 'Invalid Spotify link!'], 400);
        }
    }

    private function isSpotifyLink($input)
    {
        $pattern = '/^(https:\/\/open\.spotify\.com\/(track|album|playlist)\/[a-zA-Z0-9]+)(\?.*)?$/i';
        return preg_match($pattern, $input);
    }
}
