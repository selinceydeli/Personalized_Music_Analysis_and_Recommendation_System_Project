@props(['song'])
@props(['albumPerformers'])
@props(['albums'])
@props(['performer'])

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
                <span style="font-size: {{ strlen($song->name) > 20 ? '1.5rem' : '2rem' }}">
                    {{ $song->name }}
                </span>
            </h3>
            <i class="fas fa-clock"></i>
            @if ($song->duration)
                <span class="text-lg font-bold text-black-600">
                    ({{ formatSongDuration($song->duration) }})
                </span>
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
                <p>
                    <i class="fas fa-microphone"></i> <!-- Microphone icon -->
                    @php
                        $albumId = $song->album->album_id;
                    @endphp
                    @if (isset($albumPerformers[$albumId]))
                        @foreach ($albumPerformers[$albumId] as $albumPerformer)
                            <!-- Display performer details -->
                            @if ($albumPerformer->name !== $performer->name)
                                <a href="/performers/{{ $albumPerformer->artist_id }}?song-id={{ $song->song_id }}"
                                    class="text-lg font-bold">
                                    {{ $albumPerformer->name }}
                                </a>
                            @else
                                <span class="text-lg font-bold">
                                    {{ $albumPerformer->name }}
                                </span>
                            @endif
                            <!-- Display other performer details as needed -->
                            <!-- Check if it's the last performer in the album -->
                            @if (!$loop->last)
                                , <!-- Add a comma if it's not the last performer -->
                            @endif
                        @endforeach
                        <x-album-tags :genresCsv="$albumPerformer->genre" />
                    @else
                    @endif
                </p>
            </div>
        </div>
    </div>
</x-card>
