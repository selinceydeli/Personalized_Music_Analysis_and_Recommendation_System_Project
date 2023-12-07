<x-layout>
    <x-card class="p-10">
        <header>
            <h1 class="text-3xl text-center font-bold my-6 uppercase">
                Download Your Rated Music
            </h1>
        </header>

        <!-- List of Songs -->
        <div id="songsContainer" class="flex flex-col items-center">
            <!-- Dynamically populated list of songs will go here -->
        </div>

        <!-- Container for Centering the Download Button -->
        <div class="flex justify-center mt-4">
            <button onclick="downloadAllSongs()" class="bg-green-500 text-white px-4 py-2 rounded">Download All Rated Songs</button>
        </div>
    </x-card>
</x-layout>

<script>
    var authenticatedUsername = "{{ auth()->user()->username }}";

    window.onload = function() {
        fetchRatedSongs();
    };

    function fetchRatedSongs() {
        fetch(`/user/${authenticatedUsername}/rated-songs`)
            .then(response => response.json())
            .then(songs => {
                populateSongs(songs);
            })
            .catch(error => console.error('Error:', error));
    }

    function populateSongs(songs) {
        const container = document.getElementById('songsContainer');
        songs.forEach(song => {
            const songElement = document.createElement('div');
            songElement.className = 'mb-4 p-4 border rounded shadow-md w-full md:w-2/3';
            songElement.innerHTML = `
                <h2 class="font-bold">${song.name}</h2>
                <button onclick="downloadSong('${song.song_id}')" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Download</button>
            `;
            container.appendChild(songElement);
        });
    }

    function downloadSong(songId) {
        window.location.href = '/download-song/' + songId;
    }

    function downloadAllSongs() {
        window.location.href = '/download-all-rated-songs';
    }
</script>

