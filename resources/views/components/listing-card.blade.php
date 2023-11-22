@props(['song'])
@props(['performers'])

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
                    $songPerformers = json_decode($song->performers, true); // Decode JSON to an array
                    $matchedPerformers = [];
                @endphp
                <p>
                    <i class="fas fa-microphone"></i> <!-- Microphone icon -->
                    @foreach ($performers as $performer)
                        @if (in_array($performer->artist_id, $songPerformers))
                            @php
                                $matchedPerformers[] = $performer; // Collect matched performer names
                            @endphp
                            <a href="/performers/{{ $performer->artist_id }}?song-id={{ $song->song_id }}"
                               class="text-lg font-bold">
                                {{ $performer->name }} <!-- Assuming 'name' is the property you want to display -->
                            </a>
                            @if (!$loop->last && count($matchedPerformers) > 1)
                                , <!-- Add a comma if it's not the last performer and there's more than one matched performer -->
                            @endif
                        @endif
                    @endforeach
                </p>
                <x-listing-tags :matchedPerformers="$matchedPerformers" />
            </div>
        </div>
    </div>
</x-card>
