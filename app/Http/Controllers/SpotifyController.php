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

    public function importJSON(Request $request)
    {
        $request->validate([
            'json_file' => 'required|file|mimes:json',
        ]);

        $jsonFilePath = $request->file('json_file')->getRealPath();
        $jsonData = json_decode(file_get_contents($jsonFilePath), true);

        $spotifyLinks = $jsonData['spotify_links']; // Adjust this based on your JSON structure
        $scriptPath = 'path/to/importSongWithLink.py'; // Adjust the script path
        
        foreach ($spotifyLinks as $url) {
            if ($this->isSpotifyLink($url)) {
                $command = "python3 tempFunctions/importSongWithLink.py " . escapeshellarg($url) . " 2>&1";
                $result = shell_exec($command);
            }

            else {
                return response()->json(['message' => 'Invalid Spotify link detected!'], 400);
            }
        }

        return response()->json([
            'message' => 'Script executed',
            'output' => $result,
            'executed_command' => $command // Including the executed command can be helpful
        ]);
    }

    private function isSpotifyLink($input)
    {
        $pattern = '/^(https:\/\/open\.spotify\.com\/(track|album|playlist)\/[a-zA-Z0-9]+)(\?.*)?$/i';
        return preg_match($pattern, $input);
    }
}
