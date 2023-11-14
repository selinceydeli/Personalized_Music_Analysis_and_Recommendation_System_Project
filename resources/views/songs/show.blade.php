@if (!function_exists('formatSongDuration'))
    @php
        function formatSongDuration($seconds)
        {
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
        $totalDuration += $s->song_length_seconds; // Add current song duration to total duration
    @endphp
@endforeach

@php
    // Helper function to format date as "Day:Month:Year"
    function formatDate($date)
    {
        return date('d M Y', strtotime($date));
    }
@endphp


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
                            <a href="/performers/{{ $performer->id }}" class="text-lg font-bold">
                                {{ $performer->name }}
                            </a>
                            @if (!$loop->last)
                                , <!-- Add a comma if it's not the last performer -->
                            @endif
                        @endforeach
                    </p>
                </div>
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
                            @if ($s->song_length_seconds)
                                <span class="text-lg font-bold text-black-600">
                                    ({{ formatSongDuration($s->song_length_seconds) }})</span>
                            @endif
                        </h3>
                        <div class="text-lg mt-4">
                            <i class="fas fa-calendar"></i>
                            <strong>{{ formatDate($s->publ_date) }}</strong> <!-- Display the publishing date -->
                        </div>
                        <div>
                            <p>
                                <i class="fas fa-microphone"></i> <!-- Microphone icon -->
                                @foreach ($performers as $performer)
                                    <a href="/performers/{{ $performer->id }}" class="text-lg font-bold">
                                        {{ $performer->name }}
                                    </a>
                                    @if (!$loop->last)
                                        , <!-- Add a comma if it's not the last performer -->
                                    @endif
                                @endforeach
                            </p>
                        </div>
                        <x-album-tags :genresCsv="$song->genre" />
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</x-layout>
