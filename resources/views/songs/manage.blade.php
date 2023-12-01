<x-layout>
    <x-card class="p-10">
        <header>
            <h1 class="text-3xl text-center font-bold my-6 uppercase">
                Upload Music via Spotify
            </h1>
        </header>

        <!-- Centered Form for Spotify link input -->
        <div class="flex justify-center flex-col items-center">
            <form id="spotifyForm" action="/upload-via-spotify" method="POST" class="max-w-md flex items-center">
                @csrf
                <div class="flex items-center border-b border-b-2 border-blue-500 py-2 mr-2">
                    <input type="text" id="spotifyLink" name="spotifyLink" placeholder="Enter Spotify Link" class="border-none focus:outline-none flex-grow px-2 w-64">
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Preview</button>
                </div>
            </form>

            <!-- Display error message if the link is invalid -->
            <div id="errorMessage" class="mt-4 text-red-500" style="display: none;">
                Invalid Spotify link!
            </div>
        </div>
    </x-card>
</x-layout>

<script>
    document.getElementById('spotifyForm').addEventListener('submit', function(event) {
        event.preventDefault();
        searchSong();
    });

    function searchSong() {
        const spotifyLink = document.getElementById('spotifyLink').value;

        if (!isValidSpotifyLink(spotifyLink)) {
            // Show error message
            document.getElementById('errorMessage').classList.remove('hidden');

            // Hide error message after 5 seconds
            setTimeout(() => {
                document.getElementById('errorMessage').classList.add('hidden');
            }, 5000);

            return;
        }

        // Proceed with form submission for valid link
        document.getElementById('spotifyForm').submit();
    }

    function isValidSpotifyLink(input) {
        const pattern = /^(https:\/\/open\.spotify\.com\/(track|album|playlist)\/[a-zA-Z0-9]+)(\?.*)?$/i;
        return pattern.test(input);
    }
</script>
