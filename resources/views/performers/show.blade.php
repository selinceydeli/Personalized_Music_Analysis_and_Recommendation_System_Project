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


@if (!function_exists('formatDate'))
    @php
        // Helper function to format date as "Added at Day:Month:Year"
        function formatDate($date)
        {
            return 'Release Date: ' . date('d M Y', strtotime($date));
        }
    @endphp
@endif



<x-layout>
    <div class="mx-4 text-align: center;">
        <x-card class="p-10 relative">
            <div class="flex flex-col items-center justify-center text-center">
                <img class="w-64 h-64 mr-6 mb-6 md:block"
                    src="{{ $performer->image_url ? $performer->image_url : asset('public/images/no-album.png') }}"
                    alt="" />
                <div>
                    <p>
                        <span class="text-lg font-bold">
                            {{ $performer->name }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center mt-2">
                    @php
                        $averageRating = $performer->average_performer_rating; // Get the average rating from the ratingsMap
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
                            <i class="far fa-star text-yellow-500" style="font-size: 24px;"></i>
                            <!-- Empty star icon -->
                        @endif
                    @endfor
                    @if ($averageRating !== null)
                        <span class="text-lg ml-2">{{ number_format($averageRating, 1) }}</span>
                    @endif
                </div>
            </div>
            <div class="mt-4 text-align: center;">
                <!-- User Rating Section -->
                @if (auth()->check())
                    @php
                        $userRating = $latestPerformerRating ?? null; // Get the user rating from the ratingsMap
                    @endphp
                    <div class="text-lg" style="display: flex; flex-direction: column; align-items: center;">
                        <!-- Center-align the content -->
                        @if ($userRating !== null)
                            <p>Most Recent Rating: {{ $userRating }}</p>
                            <span>
                                Rerate this performer:
                                <form id="ratingForm_{{ $performer->artist_id }}" method="POST"
                                    action="/rateperformer">
                                    @csrf
                                    <input type="hidden" name="artist_id" value="{{ $performer->artist_id }}">
                                    <!-- Display 5 stars for rerating the song -->
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-btn" data-rating="{{ $i }}">
                                            <i class="far fa-star"></i> <!-- Empty star -->
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" id="ratingInput_{{ $performer->artist_id }}"
                                        value="{{ $userRating }}">
                                </form>
                            </span>
                        @else
                            <p>Rate this performer:</p>
                            <form id="ratingForm_{{ $performer->artist_id }}" method="POST" action="/rateperformer">
                                @csrf
                                <input type="hidden" name="artist_id" value="{{ $performer->artist_id }}">
                                <!-- Display 5 stars for rating the song -->
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" class="star-btn" data-rating="{{ $i }}">
                                        <i class="far fa-star"></i> <!-- Empty star -->
                                    </button>
                                @endfor
                                <input type="hidden" name="rating" id="ratingInput_{{ $performer->artist_id }}"
                                    value="">
                            </form>
                        @endif
                    </div>
                @endif
                @if (auth()->check())
                    <form id="deleteForm_{{ $performer->artist_id }}" method="POST" action="/deleteperformer/{{ $performer->artist_id }}"
                        class="absolute bottom-5 right-5 bg-laravel text-white p-1 rounded-full">
                        @csrf
                        <button type="submit" class="delete-song-btn" data-performer-id="{{ $performer->artist_id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </x-card>
        <!-- Section for songs list -->
        <div class="mx-4 mt-6">
            <h2 class="text-2xl font-bold mb-4">Albums</h2>
            <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
                @foreach ($albums as $album)
                    <x-card>
                        <div class="flex relative">
                            <img class="w-48 h-48 mr-6 md:block"
                                src="{{ $album->image_url ? $album->image_url : asset('/images/no-image.png') }}"
                                alt="Album Image" />
                            <div class="text-lg mt-4">
                                <a
                                    href="/albums/{{ $album->album_id }}?song-id={{ $album->songs->first()->song_id }}">
                                    {{ $album->name }}
                                </a>
                                <div class="flex items-center mt-2">
                                    @php
                                        $averageRating = $album->average_album_rating; // Get the average rating from the ratingsMap
                                        $fullStars = floor($averageRating); // Calculate the number of full stars
                                        $partialStar = $averageRating - $fullStars; // Calculate the fraction of the partial star
                                    @endphp
                                    @for ($i = 0; $i < 5; $i++)
                                        @if ($i < $fullStars)
                                            <i class="fas fa-star text-yellow-500" style="font-size: 24px;"></i>
                                            <!-- Full star icon -->
                                        @elseif ($partialStar > 0)
                                            <i class="fas fa-star-half-alt text-yellow-500"
                                                style="font-size: 24px;"></i>
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
                                <div class="mt-2">
                                    <i class="fas fa-clock"></i>
                                    @php
                                        $totalDuration = 0;
                                    @endphp

                                    @foreach ($album->songs as $s)
                                        @php
                                            $totalDuration += $s->duration;
                                        @endphp
                                    @endforeach

                                    <span class="text-lg font-bold text-black-600">
                                        ({{ formatSongDuration($totalDuration) }})</span>
                                </div>
                                <!-- Date -->
                                <div class="text-lg mt-4">
                                    <i class="fas fa-calendar"></i>
                                    <strong>{{ formatDate($album->release_date) }}</strong>
                                    <!-- Display the publishing date -->
                                </div>
                                <!-- Display album performers -->
                                <div class="mt-2">
                                    <p>
                                        <i class="fas fa-microphone"></i> <!-- Microphone icon -->
                                        @if (isset($albumPerformers[$album->album_id]))
                                            @foreach ($albumPerformers[$album->album_id] as $albumPerformer)
                                                <a href="/performers/{{ $albumPerformer->artist_id }}?song-id={{ $songId }}"
                                                    class="text-lg font-bold">
                                                    {{ $albumPerformer->name }}
                                                </a>
                                                @if (!$loop->last)
                                                    , <!-- Add a comma if it's not the last performer -->
                                                @endif
                                            @endforeach
                                        @else
                                            <p>No performers found for this album.</p>
                                        @endif
                                    </p>
                                </div>
                                @php
                                    $albumGenres = [];

                                    if (isset($albumPerformers[$album->album_id])) {
                                        foreach ($albumPerformers[$album->album_id] as $albumPerformer) {
                                            $genres = json_decode($albumPerformer->genre);
                                            $albumGenres = array_merge($albumGenres, $genres);
                                        }
                                        $albumGenres = array_unique($albumGenres);
                                    }
                                @endphp
                                <x-album-tags :genresCsv="$albumGenres" />
                            </div>
                            @if (auth()->check())
                                <form id="deleteForm_{{ $album->album_id }}" method="POST"
                                    action="/deletealbum/{{ $album->album_id }}"
                                    class="absolute bottom-5 right-5 bg-laravel text-white p-1 rounded-full">
                                    @csrf
                                    <button type="submit" class="delete-song-btn"
                                        data-album-id="{{ $album->album_id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>
        <div class="mx-4 mt-6">
            <h2 class="text-2xl font-bold mb-4">Songs</h2>
            <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
                @if (count($songs) == 0)
                    <p>
                        No music found
                    </p>
                @else
                    @php
                        $count = 0;
                    @endphp
                    @foreach ($songs as $song)
                        <div class="performer-card" data-title="{{ $song->name }}">
                            <x-performer-card :song="$song" :albumPerformers="$albumPerformers" :albums="$albums" :performer="$performer"
                                :performersSongs="$performersSongs" :count="$count" :ratingsMap="$ratingsMap" />
                        </div>
                        @php
                            $count++;
                        @endphp
                    @endforeach
                @endif
            </div>
            <div class="mt-6 p-4">
                {{ $songs->links() }}
            </div>
        </div>
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
            var artistId = $(this).closest('form').find('input[name="artist_id"]').val();
            $('#ratingInput_' + artistId).val(
                rating); // Set the hidden input value to the selected rating for this specific card

            // Submit the form for this specific card
            $('#ratingForm_' + artistId).submit();
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
