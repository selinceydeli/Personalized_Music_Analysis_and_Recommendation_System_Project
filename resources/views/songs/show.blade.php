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

@php
    $totalDuration = 0;
@endphp

@foreach ($songs as $s)
    @php
        $totalDuration += $s->duration; // Add current song duration to total duration
    @endphp
@endforeach

@if (!function_exists('formatDateAlbum'))
    @php
        // Helper function to format date as "Added at Day:Month:Year"
        function formatDateAlbum($date)
        {
            return 'Released at ' . date('d M Y', strtotime($date));
        }
    @endphp
@endif

@if (!function_exists('formatDate'))
    @php
        // Helper function to format date as "Added at Day:Month:Year"
        function formatDate($date)
        {
            return 'Added at ' . date('d M Y', strtotime($date));
        }
    @endphp
@endif



<x-layout>
    <a href="/" class="inline-block text-black ml-4 mb-4"><i class="fa-solid fa-arrow-left"></i> Back
    </a>
    <div class="mx-4 text-align: center;">
        <x-card class="p-10">
            <div class="flex flex-col items-center justify-center text-center">
                <img class="w-64 h-64 mr-6 mb-6 md:block"
                    src="{{ $album->image_url ? $album->image_url : asset('public/images/no-album.png') }}"
                    alt="" />
                <h3 class="text-2xl font-bold mb-2 md:text-center">{{ $album->name }}
                    @if ($totalDuration > 0)
                        <span class="text-xl font-bold ml-2">({{ formatSongDuration($totalDuration) }})</span>
                    @endif
                </h3>
                <div>
                    <p>
                        <span>by </span>
                        @foreach ($performers as $performer)
                            <a href="/performers/{{ $performer->artist_id }}?song-id={{ $songId }}"
                                class="text-lg font-bold">
                                {{ $performer->name }}
                            </a>
                            @if (!$loop->last)
                                , <!-- Add a comma if it's not the last performer -->
                            @endif
                        @endforeach
                    </p>
                </div>
                <div class="text-lg mt-4">
                    <!-- Display star icon for album rating -->
                    @php
                        $fullAlbumStars = floor($albumAverageRating); // Calculate the number of full stars for the album
                        $partialAlbumStar = $albumAverageRating - $fullAlbumStars; // Calculate the fraction of the partial star for the album
                    @endphp

                    {{-- Display the full stars --}}
                    @for ($i = 0; $i < $fullAlbumStars; $i++)
                        <i class="fas fa-star text-yellow-500" style="font-size: 24px;"></i>
                        <!-- Full star icon for album rating -->
                    @endfor

                    {{-- Display the partial star --}}
                    @if ($partialAlbumStar > 0)
                        <i class="fas fa-star-half-alt text-yellow-500" style="font-size: 24px;"></i>
                        <!-- Half-filled star icon for album rating -->
                    @endif

                    {{-- Display remaining empty stars --}}
                    @for ($i = ceil($albumAverageRating); $i < 5; $i++)
                        <i class="far fa-star text-yellow-500" style="font-size: 24px;"></i>
                        <!-- Empty star icon for album rating -->
                    @endfor

                    @if ($albumAverageRating !== null)
                        <span class="text-lg ml-2">{{ number_format($albumAverageRating, 1) }}</span>
                        <!-- Display the album's average rating -->
                    @endif
                </div>

                <div class="text-lg mt-4">
                    <i class="fas fa-calendar"></i>
                    <strong>{{ formatDateAlbum($album->release_date) }}</strong>
                    <!-- Display the publishing date -->
                </div>
            </div>
            <div class="mt-4 text-align: center;">
                <!-- User Rating Section -->
                @if (auth()->check())
                    @php
                        $userRating = $latestAlbumRating ?? null; // Get the user rating from the ratingsMap
                    @endphp
                    <div class="text-lg" style="display: flex; flex-direction: column; align-items: center;">
                        <!-- Center-align the content -->
                        @if ($userRating !== null)
                            <p>Most Recent Rating: {{ $userRating }}</p>
                            <span>
                                Rerate this album:
                                <form id="ratingForm_{{ $song->album->album_id }}" method="POST" action="/ratealbum">
                                    @csrf
                                    <input type="hidden" name="album_id" value="{{ $song->album->album_id }}">
                                    <!-- Display 5 stars for rerating the song -->
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-btn" data-rating="{{ $i }}">
                                            <i class="far fa-star"></i> <!-- Empty star -->
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" id="ratingInput_{{ $song->album->album_id }}"
                                        value="{{ $userRating }}">
                                </form>
                            </span>
                        @else
                            <p>Rate this album:</p>
                            <form id="ratingForm_{{ $song->album->album_id }}" method="POST" action="/ratealbum">
                                @csrf
                                <input type="hidden" name="album_id" value="{{ $song->album->album_id }}">
                                <!-- Display 5 stars for rating the song -->
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" class="star-btn" data-rating="{{ $i }}">
                                        <i class="far fa-star"></i> <!-- Empty star -->
                                    </button>
                                @endfor
                                <input type="hidden" name="rating" id="ratingInput_{{ $song->album->album_id }}"
                                    value="">
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </x-card>
        <!-- Section for songs list -->
        @foreach ($songs as $s)
            <x-card>
                <div class="flex">
                    <img class="w-48 mr-6 md:block"
                        src="{{ $s->album && $s->album->image_url ? $s->album->image_url : asset('/images/no-image.png') }}"
                        alt="" />
                    <div>
                        <h3 class="text-2xl">
                            <i class="fas fa-music"></i> {{ $s->name }}
                        </h3>
                        @php
                            $averageRating = $s->average_rating; // Get the average rating from the ratingsMap
                            $fullStars = floor($averageRating); // Calculate the number of full stars
                            $partialStar = $averageRating - $fullStars; // Calculate the fraction of the partial star
                        @endphp
                        @for ($i = 0; $i < 5; $i++)
                            @if ($i < $fullStars)
                                <i class="fas fa-star text-yellow-500" style="font-size: 24px;"></i>
                                <!-- Full star icon -->
                            @elseif ($partialStar > 0)
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
                        <div class="text-lg mt-4">
                            <i class="fas fa-clock"></i>
                            @if ($s->duration)
                                <span class="text-lg font-bold text-black-600">
                                    ({{ formatSongDuration($s->duration) }})</span>
                            @endif
                        </div>
                        <div class="text-lg mt-4">
                            <i class="fas fa-calendar"></i>
                            <strong>{{ formatDate($s->system_entry_date) }}</strong>
                            <!-- Display the publishing date -->
                        </div>
                        <div>
                            <p>
                                <i class="fas fa-microphone"></i> <!-- Microphone icon -->
                                @foreach ($performers as $performer)
                                    <a href="/performers/{{ $performer->artist_id }}?song-id={{ $songId }}"
                                        class="text-lg font-bold">
                                        {{ $performer->name }}
                                    </a>
                                    @if (!$loop->last)
                                        , <!-- Add a comma if it's not the last performer -->
                                    @endif
                                @endforeach
                            </p>
                        </div>
                        <x-album-tags :genresCsv="$genres" />
                        <div class="mt-4">
                            <!-- User Rating Section -->
                            @if (auth()->check())
                                @php
                                    $userRating = $ratingsMap[$s->song_id]['latest_user_rating'] ?? null; // Get the user rating from the ratingsMap
                                @endphp
                                <p class="text-lg">
                                    @if ($userRating !== null)
                                        Most Recent Rating: {{ $userRating }} <!-- Display the user's rating -->
                                        <br>
                                        <span>
                                            Rerate this song:
                                            <form id="ratingForm_{{ $s->song_id }}" method="POST"
                                                action="/ratesong">
                                                @csrf
                                                <input type="hidden" name="song_id" value="{{ $s->song_id }}">
                                                <!-- Display 5 stars for rerating the song -->
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <button type="button" class="star-btn"
                                                        data-rating="{{ $i }}">
                                                        <i class="far fa-star"></i> <!-- Empty star -->
                                                    </button>
                                                @endfor
                                                <input type="hidden" name="rating"
                                                    id="ratingInput_{{ $s->song_id }}" value="{{ $userRating }}">
                                            </form>
                                        </span>
                                    @else
                                        <br>
                                        Rate this song:
                                        <form id="ratingForm_{{ $s->song_id }}" method="POST" action="/ratesong">
                                            @csrf
                                            <input type="hidden" name="song_id" value="{{ $s->song_id }}">
                                            <!-- Display 5 stars for rating the song -->
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button" class="star-btn"
                                                    data-rating="{{ $i }}">
                                                    <i class="far fa-star"></i> <!-- Empty star -->
                                                </button>
                                            @endfor
                                            <input type="hidden" name="rating"
                                                id="ratingInput_{{ $s->song_id }}" value="">
                                        </form>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</x-layout>

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
            var albumId = $(this).closest('form').find('input[name="album_id"]').val();
            $('#ratingInput_' + albumId).val(
            rating); // Set the hidden input value to the selected rating for this specific card

            // Submit the form for this specific card
            $('#ratingForm_' + albumId).submit();
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
