@if (!function_exists('formatSongDuration'))
    @php
        function formatSongDuration($milliseconds)
        {
            $seconds = $milliseconds / 1000; // Convert milliseconds to seconds
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;

            if ($hours > 0) {
                return sprintf('%dh : %dm : %02ds', $hours, $remainingMinutes, $remainingSeconds);
            } else {
                return sprintf('%dm : %02ds', $minutes, $remainingSeconds);
            }
        }
    @endphp
@endif


<x-layout>
    <div class="mx-4 text-align: center;">
        <x-card class="p-10 relative">
            <div class="flex flex-col items-center justify-center text-center">
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
                    <div class="flex items-center justify-center mt-2 ml-10"> <!-- Adding ml-4 for left margin -->
                        <!-- Your star rating code -->
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

                    <i class="fas fa-clock"></i>
                    @if ($song->duration)
                        <span class="text-lg font-bold text-black-600">
                            ({{ formatSongDuration($song->duration) }})</span>
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
                        <x-album-tags :genresCsv="$matchedPerformers[0]->genre" />
                    </div>
                    <div class="attributes-section">
                        <p><strong>Song Attributes</strong></p>
                        <ul class="attributes-list">
                            <li>
                                <strong>Key:</strong> {{ $song->key }}
                            </li>
                            <li>
                                <strong>Tempo:</strong> {{ $song->tempo }}
                            </li>
                            <li>
                                <strong>Mode:</strong> {{ $song->mode }}
                            </li>
                            <li>
                                <strong>Danceability:</strong> 
                                @php
                                    $danceability = $song->danceability;
                                    $danceLabels = [
                                        '0.0 - 0.2' => 'Lead Feet',
                                        '0.2 - 0.4' => 'Easy Moves',
                                        '0.4 - 0.6' => 'Groovy',
                                        '0.6 - 0.8' => 'Dancefloor Hit',
                                        '0.8 - 1.0' => 'Dance Master'
                                    ];
                                    $danceLabel = collect($danceLabels)->filter(function ($range, $label) use ($danceability) {
                                        [$min, $max] = explode(' - ', $label);
                                        return $danceability >= $min && $danceability <= $max;
                                    })->values()->first();
                                @endphp
                                {{ $danceLabel ?? '' }}
                            </li>
                            <li>
                                <strong>Energy:</strong> 
                                @php
                                    $energy = $song->energy;
                                    $energyLabels = [
                                        '0.0 - 0.2' => 'Low Energy',
                                        '0.2 - 0.4' => 'Relaxed',
                                        '0.4 - 0.6' => 'Energetic',
                                        '0.6 - 0.8' => 'High Energy',
                                        '0.8 - 1.0' => 'Explosive'
                                    ];
                                    $energyLabel = collect($energyLabels)->filter(function ($range, $label) use ($energy) {
                                        [$min, $max] = explode(' - ', $label);
                                        return $energy >= $min && $energy <= $max;
                                    })->values()->first();
                                @endphp
                                {{ $energyLabel ?? '' }}
                            </li>
                            <li>
                                <strong>Valence:</strong> 
                                @php
                                    $valence = $song->valence;
                                    $valenceLabels = [
                                        '0.0 - 0.2' => 'Melancholic',
                                        '0.2 - 0.4' => 'Somber',
                                        '0.4 - 0.6' => 'Positive',
                                        '0.6 - 0.8' => 'Happy',
                                        '0.8 - 1.0' => 'Euphoric'
                                    ];
                                    $valenceLabel = collect($valenceLabels)->filter(function ($range, $label) use ($valence) {
                                        [$min, $max] = explode(' - ', $label);
                                        return $valence >= $min && $valence <= $max;
                                    })->values()->first();
                                @endphp
                                {{ $valenceLabel ?? '' }}
                            </li>
                        </ul>
                    </div>
                    <div class="mt-4 text-align: center;">
                        <!-- User Rating Section -->
                        @if (auth()->check())
                            @php
                                $userRating = $ratingsMap[$song->song_id]['latest_user_rating'] ?? null; // Get the user rating from the ratingsMap
                            @endphp
                            <div class="text-lg" style="display: flex; flex-direction: column; align-items: center;">
                                <!-- Center-align the content -->
                                @if ($userRating !== null)
                                    <p>Most Recent Rating: {{ $userRating }}</p>
                                    <span>
                                        Rerate this song:
                                        <form id="ratingForm_{{ $song->song_id }}" method="POST" action="/ratesong">
                                            @csrf
                                            <input type="hidden" name="song_id" value="{{ $song->song_id }}">
                                            <!-- Display 5 stars for rerating the song -->
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button" class="star-btn"
                                                    data-rating="{{ $i }}">
                                                    <i class="far fa-star"></i> <!-- Empty star -->
                                                </button>
                                            @endfor
                                            <input type="hidden" name="rating" id="ratingInput_{{ $song->song_id }}"
                                                value="{{ $userRating }}">
                                        </form>
                                    </span>
                                @else
                                    <p>Rate this song:</p>
                                    <form id="ratingForm_{{ $song->song_id }}" method="POST" action="/ratesong">
                                        @csrf
                                        <input type="hidden" name="song_id" value="{{ $song->song_id }}">
                                        <!-- Display 5 stars for rating the song -->
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button type="button" class="star-btn" data-rating="{{ $i }}">
                                                <i class="far fa-star"></i> <!-- Empty star -->
                                            </button>
                                        @endfor
                                        <input type="hidden" name="rating" id="ratingInput_{{ $song->song_id }}"
                                            value="">
                                    </form>
                                @endif
                            </div>
                        @endif
                        @if (auth()->check())
                            <form id="deleteForm_{{ $song->song_id }}" method="POST"
                                action="/deletesong/{{ $song->song_id }}"
                                class="absolute bottom-8 right-8 bg-red-500 text-white p-1 rounded-full"
                                style="margin-bottom: 20px;"> <!-- Added margin-bottom to move the button higher -->
                                @csrf
                                <button type="submit" class="delete-song-btn" data-song-id="{{ $song->song_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
        </x-card>
    </div>
    <p style="font-weight: bold;">Similar Songs</p>
    <div>
        @foreach ($relatedSongs as $relatedSong)
            <x-card class="relative p-4"> <!-- Added padding -->
                <div class="flex items-center"> <!-- Added items-center to align vertically -->
                    <img class="w-24 h-24 mr-4 md:block"
                        src="{{ $relatedSong->album && $relatedSong->album->image_url ? $relatedSong->album->image_url : asset('/images/no-image.png') }}"
                        alt="" />
                    <div>
                        <h3 class="text-lg">
                            <i class="fas fa-music"></i>
                            <a href="/songs/{{ $relatedSong->song_id }}" style="font-size: 1.25rem">
                                <!-- Adjusted font size -->
                                {{ $relatedSong->name }}
                            </a>
                        </h3>
                        <i class="fas fa-clock"></i>
                        @if ($relatedSong->duration)
                            <span class="text-sm text-gray-600">
                                ({{ formatSongDuration($relatedSong->duration) }})
                            </span>
                        @endif

                        @if ($relatedSong->album)
                            <div class="text-sm mt-2">
                                <i class="fas fa-folder"></i>
                                <strong>
                                    <a
                                        href="/albums/{{ $relatedSong->album->album_id }}?song-id={{ $relatedSong->song_id }}">
                                        {{ $relatedSong->album->name }}
                                    </a>
                                </strong>
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</x-layout>

<style>
    .related-songs-container {
        max-height: 400px;
        /* Set a fixed height for the container */
        overflow-y: auto;
        /* Enable vertical scrolling */
        padding: 20px 0;
        /* Adjust padding as needed */
    }

    .scrollable-songs {
        display: flex;
        gap: 20px;
        /* Adjust the gap between songs */
        padding: 0 20px;
        /* Adjust padding as needed */
    }

    .related-song {
        flex: 0 0 auto;
        width: 300px;
        /* Adjust the width of each song card */
        /* Other styles for the song card */
    }
</style>

<style>
    .attributes-section {
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 16px;
        margin-top: 20px;
        background-color: #f9f9f9;
    }

    .section-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .attributes-list {
        display: flex;
        flex-direction: column;
        gap: 8px; /* Adjust the gap between attributes */
    }

    .attributes-list li {
        display: flex;
        align-items: center;
    }

    .attributes-list strong {
        margin-right: 8px;
    }

    .attribute-label {
        padding: 4px 8px;
        border-radius: 4px;
        background-color: #ffc107;
        color: #333;
        font-weight: bold;
    }
</style>


<script>
    $(document).ready(function() {
        var currentRating = parseInt($('#ratingInput').val()) || 0;

        $(document).on('mouseover', '.star-btn', function() {
            var rating = $(this).data('rating');

            $(this).siblings().addBack().find('i').each(function(index) {
                if (index < rating) {
                    $(this).removeClass('far').addClass('fas'); // Fill stars
                } else {
                    $(this).removeClass('fas').addClass('far'); // Empty stars
                }
            });
        });

        $(document).on('click', '.star-btn', function() {
            var rating = $(this).data('rating');
            var songId = $(this).closest('form').find('input[name="song_id"]').val();
            $('#ratingInput_' + songId).val(
                rating); // Set the hidden input value to the selected rating for this specific card

            // Submit the form for this specific card
            $('#ratingForm_' + songId).submit();
        });

        $(document).on('mouseout', '.star-rating', function(e) {
            // Check if the mouse leaves the star rating area
            if (!$(e.relatedTarget).hasClass('star-btn')) {
                $('.star-btn i').each(function(index) {
                    if (index < currentRating) {
                        $(this).removeClass('far').addClass('fas-colored'); // Fill stars
                    } else {
                        $(this).removeClass('fas-colored').addClass('far'); // Empty stars
                    }
                });
            }
        });
    });
</script>
