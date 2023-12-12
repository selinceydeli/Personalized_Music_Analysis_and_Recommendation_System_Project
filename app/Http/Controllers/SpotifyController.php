<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    public function importSong(Request $request)
    {
        $url = $request->input('spotifyLink');

        // Check if the URL is a valid Spotify link
        if (!$this->isSpotifyLink($url)) {
            return response()->json(['message' => 'Invalid Spotify link!'], 400);
        }

        // Execute the Python script and capture the output, including any errors
        $command = "python tempFunctions/importSongWithLink.py " . escapeshellarg($url) . " 2>&1";
        $result = shell_exec($command);

        // Check the result of the script execution
        if (strpos($result, "Duplicate entry") !== false) {
            // Song already exists in the database
            return response()->json(['message' => 'Song already exists'], 409);
        } else if (strpos($result, "Error") !== false) {
            // Handle other errors
            return response()->json(['message' => 'Error occurred during import', 'output' => $result], 500);
        } else {
            // Successful upload
            return response()->json(['message' => 'Successful Upload', 'output' => $result], 200);
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
