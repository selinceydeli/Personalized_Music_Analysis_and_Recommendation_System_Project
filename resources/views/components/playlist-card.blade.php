@props(['song'])
@props(['albumPerformers'])
@props(['albums'])
@props(['performer'])
@props(['performersSongs'])
@props(['count'])
@props(['ratingsMap'])
@props(['playlist'])
@props(['composers'])

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
            <a href="/songs/{{$song->song_id}}" class="text-2xl flex items-center">
                <i class="fas fa-music mr-2"></i>
                <span style="font-size: {{ strlen($song->name) > 20 ? '1.5rem' : '2rem' }}">
                    {{ $song->name }}
                </span>
            </a>
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
                    @foreach ($performersSongs[$count] as $performerId => $p)
                        <!-- Display performer details -->
                        <a href="/performers/{{ $performerId }}?song-id={{ $song->song_id }}"
                            class="text-lg font-bold">
                            {{ $p->name }}
                        </a>
                        <!-- Check if it's the last performer in the array -->
                        @unless ($loop->last)
                            , <!-- Add a comma if it's not the last performer -->
                        @endunless
                    @endforeach
                </p>
            </div>
            <x-album-tags :genresCsv="$performer->genre" />
            <div class="mt-10">
                <!-- User Rating Section -->
                @if (auth()->check() && in_array(auth()->user()->username, $composers))
                    <form id="deleteForm_{{ $song->song_id }}" method="POST"
                        action="/remove/{{$playlist->id}}/song/{{$song->song_id}}"
                        class="absolute bottom-5 right-5 bg-red-500 text-white p-1 rounded-full">
                        @csrf
                        <button type="submit" class="delete-song-btn" data-song-id="{{ $song->song_id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-card>