@if($recommendations && count($recommendations) > 0)
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Recommended Songs</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($recommendations as $song)
                <div class="border rounded-lg p-4 shadow-lg">
                    <h3 class="text-lg font-semibold">{{ $song['name'] }}</h3>
                    <p>Artist ID: {{ implode(', ', $song['performers']) }}</p>
                    <p>Duration: {{ gmdate('i:s', $song['duration'] / 1000) }}</p>
                    <p>Tempo: {{ $song['tempo'] }}</p>
                    <p>Key: {{ $song['key'] }}</p>
                    <p>Mode: {{ $song['mode'] }}</p>
                    @if($song['explicit'])
                        <p><strong>Explicit Content</strong></p>
                    @endif
                    <p>Average Rating: {{ collect($song['ratings'])->avg('rating') }}</p>
                    <!-- Additional song details can be added here as needed -->
                </div>
            @endforeach
        </div>
    </div>
@else
    <p class="p-6">No recommendations available at the moment.</p>
@endif
