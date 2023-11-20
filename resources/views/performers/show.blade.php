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
    <a href="/" class="inline-block text-black ml-4 mb-4"><i class="fa-solid fa-arrow-left"></i> Back
    </a>
    <div class="mx-4 text-align: center;">
        <x-card class="p-10">
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
            </div>
        </x-card>
        <!-- Section for songs list -->
        <div class="mx-4 mt-6">
            <h2 class="text-2xl font-bold mb-4">Albums</h2>
            <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
                @foreach ($albums as $album)
                    <x-card>
                        <div class="flex">
                            <img class="w-48 mr-6 md:block"
                                src="{{ $album->image_url ? $album->image_url : asset('/images/no-image.png') }}"
                                alt="Album Image" />
                            <div class="text-lg mt-4">
                                    <a href="/albums/{{ $album->album_id }}?song-id={{ $album->songs->first()->song_id }}">
                                        {{ $album->name }}
                                    </a>
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
                    @foreach ($songs as $song)
                        <div class="listing-card" data-title="{{ $song->name }}">
                            <x-performer-card :song="$song" :performerToSongs="$performerToSongs" />
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="mt-6 p-4">
                {{ $songs->links() }}
            </div>
        </div>
    </div>
</x-layout>
