@props(['song'])

    <!-- Item 1 -->
<x-card>
    <div class="flex">
        <div>
            <h3 class="text-2xl">
                <i class="fas fa-music"></i><a href="/songs/{{$song->id}}"> {{$song->name}}</a>
            </h3>
            @if ($song->album)
            <div class="text-lg mt-4">
                <i class="fas fa-folder"></i>
                <strong>
                    <a href="/albums/{{$song->album->id}}">
                        {{ $song->album->name }}
                    </a>
                </strong>
            </div>
            @endif
           <x-listing-tags :genresCsv="$song->genre"/>
        </div>
    </div>
</x-card>


