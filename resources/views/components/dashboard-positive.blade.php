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
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">
            Tailored Recommendations for {{ auth()->user()->name }}
        </h1>
        <!-- Download Button -->
        <div class="text-center mb-4">
            <button onclick="downloadPositiveRecommendations()" class="bg-blue-500 text-white px-4 py-2 rounded">Download Recommendations</button>
        </div>

        <!-- Section for General Recommendations -->
        @if ($recommendations && count($recommendations) > 0)
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">Energetic Song Mix For You</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($recommendations as $song)
                        <div class="border rounded-lg p-4 shadow-lg">
                            <img src="{{ $song->album->image_url ?? asset('/images/no-image.png') }}"
                                alt="{{ $song->name }}" class="w-full h-auto mb-3">
                            <h3 class="text-lg font-semibold">{{ $song['name'] }}</h3>
                            <p>Valence: {{ $song->valence }}</p>
                            <i class="fas fa-folder"></i>
                            <strong>
                                <a href="/albums/{{ $song->album->album_id }}?song-id={{ $song->song_id }}">
                                    {{ $song->album->name }}
                                </a>
                            </strong>
                            <div>
                                <i class="fas fa-clock"></i>
                                @if ($song->duration)
                                    <span class="text-lg font-bold text-black-600">
                                        ({{ formatSongDuration($song->duration) }})</span>
                                @endif
                            </div>
                            <div class="flex items-center mt-2">
                                @php
                                    $averageRating = $song->average_rating; // Get the average rating from the ratingsMap
                                    $fullStars = floor($averageRating); // Calculate the number of full stars
                                    $partialStar = $averageRating - $fullStars; // Calculate the fraction of the partial star
                                @endphp
                                @for ($i = 0; $i < 5; $i++)
                                    @if ($i < $fullStars)
                                        <i class="fas fa-star text-yellow-500" style="font-size: 24px;"></i>
                                        <!-- Full star icon -->
                                    @elseif ($partialStar >= 0.01)
                                        <i class="fas fa-star-half-alt text-yellow-500" style="font-size: 24px;"></i>
                                        <!-- Half-filled star icon -->
                                        @php $partialStar = 0; @endphp <!-- Set partialStar to 0 to avoid more half stars -->
                                    @else
                                        <i class="far fa-star text-yellow-500" style="font-size: 24px;"></i>
                                        <!-- Empty star icon -->
                                    @endif
                                @endfor

                                @if ($averageRating !== null)
                                    <span class="text-lg ml-2">{{ number_format($averageRating, 2) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="p-6">No recommendations available at the moment.</p>
        @endif
    </div>
</x-layout>

<script>
    function downloadPositiveRecommendations() {
        window.location.href = '/download-positive-recommendations';
    }
</script>
