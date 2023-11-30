<?php
function isSpotifyLink($input) {
    $pattern = '/^(https:\/\/open\.spotify\.com\/(track|album|playlist)\/[a-zA-Z0-9]+)(\?.*)?$/i';

    if (preg_match($pattern, $input)) {
        return true; 
    } else {
        return false;
    }
}
echo "Please enter the Spotify link of a song: ";
$url = fgets(STDIN);
if (isSpotifyLink($url)) {
    $result = shell_exec("python importSongWithLink.py ".$url);
    echo "Successful\n";
} else {
    echo "Invalid Spotify link!";
}


?>
