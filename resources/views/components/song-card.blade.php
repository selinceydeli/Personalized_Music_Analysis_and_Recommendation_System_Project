@props(['song'])
@props(['performers'])
@props(['ratingsMap'])
@props(['playlist'])

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

<x-card class="relative">
    <div class="flex">
        <img class="w-48 h-48 mr-6 md:block"
            src="{{ $song->album && $song->album->image_url ? $song->album->image_url : asset('/images/no-image.png') }}"
            alt="" />
        <div>
            <h3 class="text-2xl">
                <i class="fas fa-music"></i>
                <a href="/songs/{{ $song->song_id }}" style="font-size: 1.5rem">
                    {{ $song->name }}
                </a>
            </h3>
            <div class="flex items-center mt-2">
                @php
                    $averageRating = $song->average_rating; // Get the average rating from the ratingsMap
                    $fullStars = floor($averageRating); // Calculate the number of full stars
                    $partialStar = $averageRating - $fullStars; // Calculate the fraction of the partial star
                @endphp
                @for ($i = 0; $i < 5; $i++)
                    @if ($i < $fullStars)
                        <i class="fas fa-star text-yellow-500" style="font-size: 24px;"></i> <!-- Full star icon -->
                    @elseif ($partialStar >= 0.01)
                        <i class="fas fa-star-half-alt text-yellow-500" style="font-size: 24px;"></i>
                        <!-- Half-filled star icon -->
                        @php $partialStar = 0; @endphp <!-- Set partialStar to 0 to avoid more half stars -->
                    @else
                        <i class="far fa-star text-yellow-500" style="font-size: 24px;"></i> <!-- Empty star icon -->
                    @endif
                @endfor

                @if ($averageRating !== null)
                    <span class="text-lg ml-2">{{ number_format($averageRating, 2) }}</span>
                @endif
            </div>
            <i class="fas fa-clock"></i>
            @if ($song->duration)
                <span class="text-lg font-bold text-black-600"> ({{ formatSongDuration($song->duration) }})</span>
            @endif

            @if ($song->album)
                <div class="text-lg mt-4">
                    <i class="fas fa-folder"></i>
                    <strong>
                        <a href="/albums/{{ $song->album->album_id }}?song-id={{ $song->song_id }}">
                            {{ $song->album->name }}
                        </a>
                    </strong>
                </div>
            @endif
            <div>
                @php
                    $songPerformers = $song->performers;
                    $matchedPerformers = [];
                @endphp
                <p>
                    <i class="fas fa-microphone"></i> <!-- Microphone icon -->
                    @php $matchedPerformers = collect([]); @endphp
                    @foreach ($performers as $performer)
                        @foreach ($performer as $details)
                            @if (in_array($details->artist_id, $songPerformers) && !$matchedPerformers->contains('artist_id', $details->artist_id))
                                @php $matchedPerformers->push($details); @endphp
                            @endif
                        @endforeach
                    @endforeach
                    @php
                        $matchedPerformers = $matchedPerformers->sortBy(function ($detail) {
                            return $detail->name;
                        });
                    @endphp

                    @foreach ($matchedPerformers as $index => $details)
                        <a href="/performers/{{ $details->artist_id }}?song-id={{ $song->song_id }}"
                            class="text-lg font-bold">
                            {{ $details->name }} <!-- Assuming 'name' is the property you want to display -->
                        </a>
                        @if (!$loop->last)
                            , <!-- Add a comma if it's not the last performer -->
                        @endif
                    @endforeach
                </p>
                @php
                    // Initialize an empty array for genres
                    $genres = [];

                    // Loop through $matchedPerformers to extract genres
                    foreach ($matchedPerformers as $performer) {
                        // Check if $performer has a 'genre' property and decode it into an array
                        if (isset($performer->genre)) {
                            $decodedGenres = json_decode($performer->genre, true);

                            if (is_array($decodedGenres)) {
                                $genres = array_merge($genres, $decodedGenres);
                            }
                        }
                    }

                    // Remove duplicates from the genres array
                    $genres = array_unique($genres);
                @endphp

                @php
                    $genres = collect($genres);
                    $showAll = false;
                @endphp

                <!-- Display the first 5 genres -->
                <ul class="flex" style="position: relative; overflow: hidden;">
                    @foreach ($genres->take(5) as $index => $genre)
                        <li class="flex items-center justify-center genre-item"
                            style="background-color: red; color: white; border-radius: 0.25rem; padding: 0.5rem 0.75rem; margin-right: 0.5rem; font-size: 0.75rem;">
                            {{ $genre }}
                        </li>
                    @endforeach
                </ul>

                <!-- Hidden Genres -->
                <ul class="flex hidden-genre-list"
                    style="position: relative; overflow: hidden; margin-top: 10px; display: none;">
                    <!-- Adjust margin-top as needed -->
                    <!-- Display the remaining genres (initially hidden) -->
                    @foreach ($genres->slice(5) as $index => $genre)
                        <li class="flex items-center justify-center genre-item hidden-genre-item"
                            style="background-color: red; color: white; border-radius: 0.25rem; padding: 0.5rem 0.75rem; margin-right: 0.5rem; font-size: 0.75rem;">
                            {{ $genre }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-10">
                @if (auth()->check())
                    <form id="addForm_{{ $song->song_id }}" method="POST"
                        action="/addsongs/{{ $playlist->id }}/{{ $song->song_id }}"
                        class="absolute bottom-5 right-5 bg-pink-300 text-white p-1 rounded-full">
                        @csrf
                        <button type="submit" class="add-song-btn" data-song-id="{{ $song->song_id }}">
                            <i class="fas fa-plus"></i> <!-- Plus sign icon -->
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-card>
