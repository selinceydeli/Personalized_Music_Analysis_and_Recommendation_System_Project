<x-layout>
    @include('partials._hero')
    @include('partials._search')
    <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
        @if (count($songs) == 0)
            <p>
                No music found
            </p>
        @else
            @foreach ($songs as $song)
                <div class="listing-card" data-title="{{ $song->name }}">
                    <x-listing-card :song="$song" :performers="$performers" />
                </div>
            @endforeach
        @endif
    </div>
    <div class="mt-6 p-4">
        {{ $songs->appends(['genre' => $selectedGenre])->links() }}
    </div>
</x-layout>
