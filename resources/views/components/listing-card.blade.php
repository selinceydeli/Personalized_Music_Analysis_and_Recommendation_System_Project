@props(['song'])
@props(['performers'])
@props(['ratingsMap'])

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

<x-card>
    <div class="flex">
        <img class="w-48 mr-6 md:block"
            src="{{ $song->album && $song->album->image_url ? $song->album->image_url : asset('/images/no-image.png') }}"
            alt="" />
        <div>
            <h3 class="text-2xl">
                <i class="fas fa-music"></i>
                <span style="font-size: 1.5rem">
                    {{ $song->name }}
                </span>
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
                    @elseif ($partialStar > 0)
                        <i class="fas fa-star-half-alt text-yellow-500" style="font-size: 24px;"></i>
                        <!-- Half-filled star icon -->
                        @php $partialStar = 0; @endphp <!-- Set partialStar to 0 to avoid more half stars -->
                    @else
                        <i class="far fa-star text-yellow-500" style="font-size: 24px;"></i> <!-- Empty star icon -->
                    @endif
                @endfor

                @if ($averageRating !== null)
                    <span class="text-lg ml-2">{{ number_format($averageRating, 1) }}</span>
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

                    @foreach ($matchedPerformers as $index => $details)
                        <a href="/performers/{{ $details->artist_id }}?song-id={{ $song->song_id }}"
                            class="text-lg font-bold">
                            {{ $details->name }} <!-- Assuming 'name' is the property you want to display -->
                        </a>
                        @if (!$loop->last && $index !== $matchedPerformers->count() - 1)
                            , <!-- Add a comma if it's not the last performer -->
                        @endif
                    @endforeach
                </p>
                <x-listing-tags :matchedPerformers="$matchedPerformers" />
            </div>
            <!-- Your HTML code -->
            <div class="mt-4">
                <!-- User Rating Section -->
                @if (auth()->check())
                    @php
                        $userRating = $ratingsMap[$song->song_id]['latest_user_rating'] ?? null; // Get the user rating from the ratingsMap
                    @endphp
                    <p class="text-lg">
                        @if ($userRating !== null)
                            Most Recent Rating: {{ $userRating }} <!-- Display the user's rating -->
                            <br>
                            <span>
                                Rerate this song:
                                <form id="ratingForm_{{ $song->song_id }}" method="POST"
                                    action="/rate">
                                    @csrf
                                    <input type="hidden" name="song_id" value="{{ $song->song_id }}">
                                    <!-- Display 5 stars for rerating the song -->
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-btn" data-rating="{{ $i }}">
                                            <i class="far fa-star"></i> <!-- Empty star -->
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" id="ratingInput_{{ $song->song_id }}"
                                        value="{{ $userRating }}">
                                </form>
                            </span>
                        @else
                            <br>
                            Rate this song:
                            <form id="ratingForm_{{ $song->song_id }}" method="POST"
                                action="/rate">
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
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-card>

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
