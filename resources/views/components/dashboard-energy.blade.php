@if (!function_exists('formatSongDuration'))
    @php
        function formatSongDuration($milliseconds)
        {
            $seconds = $milliseconds / 1000; // Convert milliseconds to seconds
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            return sprintf('%dm : %02ds', $minutes, $remainingSeconds);
        }
    @endphp
@endif

<x-layout>
    <a href="/" class="inline-block text-black ml-4 mb-4"><i class="fa-solid fa-arrow-left"></i> Back
    </a>
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">
            Tailored Recommendations for {{ auth()->user()->name }}
        </h1>

        <!-- Section for General Recommendations -->
        @if($recommendations && count($recommendations) > 0)
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">Dynamic Beats: Tailored Picks Matching Your Energy & Dance Vibes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($recommendations as $song)
                        <div class="border rounded-lg p-4 shadow-lg">
                            <img src="{{ $song['album']['image_url'] ?? asset('/images/no-image.png') }}" alt="{{ $song['name'] }}" class="w-full h-auto mb-3">
                            <h3 class="text-lg font-semibold">{{ $song['name'] }}</h3>
                            <i class="fas fa-folder"></i>
                            <strong>
                                <a href="/albums/{{ $song->album->album_id }}?song-id={{ $song->song_id }}">
                                    {{ $song->album->name }}
                                </a>
                            </strong>
                        <div>
                            <i class="fas fa-clock"></i>
                            @if ($song->duration)
                                <span class="text-lg font-bold text-black-600"> ({{ formatSongDuration($song->duration) }})</span>
                            @endif
                        </div>
             
                            <p>Average Rating: {{ collect($song['ratings'])->avg('rating') }}</p>
                            <p>Energy: {{ collect($song['energy']) }}</p>
                            <p>Danceability: {{ collect($song['danceability']) }}</p>
                            <!-- Additional song details can be added here as needed -->
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="p-6">No recommendations available at the moment.</p>
        @endif
    </div>
</x-layout>
