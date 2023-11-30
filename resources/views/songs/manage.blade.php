<x-layout>
    <x-card class="p-10">
        <header>
            <h1 class="text-3xl text-center font-bold my-6 uppercase">
                Upload Music via Spotify
            </h1>
        </header>

        <!-- Centered Form for Spotify link input -->
        <div class="flex justify-center flex-col items-center">
            <form id="spotifyForm" onsubmit="return false;" class="max-w-md flex items-center">
                @csrf
                <div class="flex items-center border-b border-b-2 border-blue-500 py-2 mr-2">
                    <input type="text" id="spotify-link" name="spotifyLink" placeholder="Enter Spotify Link" class="border-none focus:outline-none flex-grow px-2 w-64">
                </div>
                <div>
                    <button type="button" onclick="searchSong()" class="bg-blue-500 text-white px-4 py-2 rounded">Preview</button>
                </div>
            </form>

            <!-- Display song information preview or error message -->
            <div id="songPreview" class="mt-4">
                <!-- Content will be dynamically updated here -->
            </div>
        </div>
    </x-card>
</x-layout>

<script>
    // Capture form submission event and prevent default action
    document.getElementById('spotifyForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission
        searchSong(); // Call searchSong function when the form is submitted
    });

    function searchSong() {
        const spotifyLink = document.getElementById('spotify-link').value;

        // Validate the Spotify link
        if (!isValidSpotifyLink(spotifyLink)) {
            // If the link is invalid, show an error message
            document.getElementById('songPreview').innerHTML = `
                <p id="errorMessage" class="text-red-500">Invalid Spotify link!</p>
            `;

            // Hide the error message after 5 seconds
            setTimeout(() => {
                document.getElementById('errorMessage').style.display = 'none';
            }, 5000); // Hide after 5 seconds (5000 milliseconds)
            return; // Exit the function if the link is invalid
        }

        // Perform logic to fetch song details from Spotify using AJAX or form submission
        // You can use the SpotifyController and its method for handling song import
        // This logic would involve making an AJAX request to the SpotifyController
        
        // Assuming you're using Axios for AJAX requests
        // Replace the URL with the correct route
        axios.post('{{ route('upload.spotifysong') }}', {
            spotifyLink: spotifyLink
        })
        .then(response => {
            // Handle the response and display song information in the songPreview section
            // For example:
            const songDetails = response.data;
            document.getElementById('songPreview').innerHTML = `
                <p>Song: ${songDetails.songName}</p>
                <p>Performer: ${songDetails.performer}</p>
                <p>Album: ${songDetails.albumName}</p>
                <p>Duration: ${songDetails.duration}</p>
                <button onclick="addSong()" class="bg-green-500 text-white px-4 py-2 rounded">Add Song</button>
            `;
        })
        .catch(error => {
            // Handle errors here
            console.error(error);
        });
    }

    function isValidSpotifyLink(input) {
        const pattern = /^(https:\/\/open\.spotify\.com\/(track|album|playlist)\/[a-zA-Z0-9]+)(\?.*)?$/i;
        return pattern.test(input);
    }

    function addSong() {
        // Logic to add the song to the system after user confirmation
        // This could involve another AJAX request or form submission
    }
</script>
