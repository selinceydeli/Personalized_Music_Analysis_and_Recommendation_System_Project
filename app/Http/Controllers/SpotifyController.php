<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    public function importSong(Request $request)
    {
        $url = $request->input('spotifyLink');

        if ($this->isSpotifyLink($url)) {
            // Execute the Python script and capture the output, including any errors
            $command = "python3 tempFunctions/importSongWithLink.py " . escapeshellarg($url) . " 2>&1";
            $result = shell_exec($command);

            // Return the result of the shell_exec command for debugging
            return response()->json([
                'message' => 'Script executed',
                'output' => $result,
                'executed_command' => $command // Including the executed command can be helpful
            ]);
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
