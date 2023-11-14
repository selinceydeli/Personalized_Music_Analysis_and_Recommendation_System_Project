@props(['song'])

<!-- Item 1 -->
@if (!function_exists('formatSongDuration'))
    @php
        function formatSongDuration($seconds)
        {
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
                <i class="fas fa-music"></i>  {{ $song->name }}
                @if ($song->song_length_seconds)
                    <span class="text-lg font-bold text-black-600"> ({{ formatSongDuration($song->song_length_seconds) }})</span>
                @endif
            </h3>
            @if ($song->album)
                <div class="text-lg mt-4">
                    <i class="fas fa-folder"></i>
                    <strong>
                        <a href="/albums/{{ $song->album->id }}?song-id={{ $song->id }}">
                            {{ $song->album->name }}
                        </a>                        
                    </strong>
                </div>
            @endif
            <x-listing-tags :genresCsv="$song->genre" />
        </div>
    </div>
</x-card>
