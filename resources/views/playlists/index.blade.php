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


@if (!function_exists('formatDate'))
    @php
        // Helper function to format date as "Added at Day:Month:Year"
        function formatDate($date)
        {
            return 'Created at ' . date('d M Y', strtotime($date));
        }
    @endphp
@endif



<x-layout>
    <div class="mx-4 text-align: center;">
        <x-card class="p-10 relative">
            <div class="flex flex-col items-center justify-center text-center">
                <img class="w-64 h-64 mr-6 mb-6 md:block"
                    src="{{ $playlist['image_url'] ? $playlist['image_url'] : asset('/images/playlist.png') }}"
                    alt="" />
                <h3 class="text-2xl font-bold mb-2 md:text-center">{{ $playlist['playlist_name'] }}
                    @if ($totalDuration > 0)
                        <span class="text-xl font-bold ml-2">({{ formatSongDuration($totalDuration) }})</span>
                    @endif
                </h3>
                <div>
                    <p>
                        <span>created by </span>
                        @php
                            $usersCount = $playlist->users->count();
                            $displayCount = min(4, $usersCount);
                            $remainingUsers = max(0, $usersCount - $displayCount);
                        @endphp
                        @foreach ($playlist->users->take($displayCount) as $index => $us)
                            <a href="/user/profile/{{ $us->username }}" class="text-lg font-bold">
                                {{ $us->username }}
                            </a>
                            @if ($index < $displayCount - 1)
                                , <!-- Add a comma if it's not the last displayed performer -->
                            @endif
                        @endforeach

                        @if ($remainingUsers > 0)
                            @if ($displayCount < $usersCount)
                                <!-- Show an indication that there are more users -->
                                and {{ $remainingUsers }} more
                            @endif
                        @endif
                    </p>
                </div>
                <div class="text-lg mt-4">
                    <i class="fas fa-calendar"></i>
                    <strong>{{ formatDate($playlist['created_at']) }}</strong>
                    <!-- Display the publishing date -->
                </div>
            </div>
        </x-card>
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
                        @php
                            $performer = null;
                            $songPerformers = $song->performers; // Decode the JSON performers data
                            // Check if performers exist and get the first one
                            if (!empty($songPerformers)) {
                                $firstPerformerId = $songPerformers[0]; // Get the ID of the first performer
                                foreach($performersSongs as $performersSong)
                                // Check if the performer ID exists in the $performersSongs array
                                if (isset($performersSong[$firstPerformerId])) {
                                    $performer = $performersSong[$firstPerformerId];
                                }
                            }
                        @endphp
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
