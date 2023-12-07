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

            
            //if ($result === null) {
                //return response()->json([
                    //'message' => 'Python is not found. Please install Python on the server.',
                //], 500);
            //}
            

            // Store song information in the session flash data
            //Session::flash('song_info', $result);

            // Redirect the user to the root URL ("/") with a success message
            return redirect('/')->with('message', 'Song information uploaded successfully!');
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

        $counter = 0;
        
        foreach ($spotifyLinks as $url) {
            $counter++;
            if ($this->isSpotifyLink($url)) {
                $command = "python3 tempFunctions/importSongWithLink.py " . escapeshellarg($url) . " 2>&1";
                $result = shell_exec($command);
            }

            else {
                return redirect('/')->with('message', 'Invalid Spotify Link at Song ' . $counter);
            }
        }
        return redirect('/')->with('message', 'Song information uploaded successfully!');
    }

    private function isSpotifyLink($input)
    {
        $pattern = '/^(https:\/\/open\.spotify\.com\/(track|album|playlist)\/[a-zA-Z0-9]+)(\?.*)?$/i';
        return preg_match($pattern, $input);
    }
}
