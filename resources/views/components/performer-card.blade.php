@props(['song'])

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
                <p>
                    <i class="fas fa-microphone"></i> <!-- Microphone icon -->
                    @if (is_string($song->performers))
                        @php
                            $performers = json_decode($song->performers);
                        @endphp

                        @if ($performers)
                            @foreach ($performers as $performer)
                                <a href="/performers/{{ $performer}}?song-id={{$song->song_id}}" class="text-lg font-bold">
                                    {{ $performer }} <!-- Assuming 'name' is the property you want to display -->
                                </a>
                                @if (!$loop->last)
                                    , <!-- Add a comma if it's not the last performer -->
                                @endif
                            @endforeach
                        @endif
                    @endif
                </p>
            </div>
            <x-album-tags :genresCsv="$performers" />
        </div>
    </div>
</x-card>
