<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    public function importSong(Request $request)
    {
        $url = $request->input('spotifyLink');

        if ($this->isSpotifyLink($url)) {
            $result = shell_exec("python3 tempFunctions/importSongWithLink.py" . escapeshellarg($url));
            return response()->json(['message' => 'Successful']);
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
