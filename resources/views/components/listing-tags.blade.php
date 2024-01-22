@props(['matchedPerformers'])

@php
    // Initialize an empty array for genres
    $genres = [];

    // Loop through $matchedPerformers to extract genres
    foreach ($matchedPerformers as $performer) {
        // Check if $performer has a 'genre' property and decode it into an array
        if (isset($performer->genre)) {
            $decodedGenres = json_decode($performer->genre, true);

            if (is_array($decodedGenres)) {
                $genres = array_merge($genres, $decodedGenres);
            }
        }
    }

    // Remove duplicates from the genres array
    $genres = array_unique($genres);
@endphp

@php 
    $genres = collect($genres);
    $showAll = false;
@endphp

<!-- Display the first 5 genres -->
<ul class="flex" style="position: relative; overflow: hidden;">
    @foreach($genres->take(5) as $index => $genre)
        <li class="flex items-center justify-center genre-item bg-laravel" style=" color: white; border-radius: 0.25rem; padding: 0.5rem 0.75rem; margin-right: 0.5rem; font-size: 0.75rem;">
            <a href="/?genre={{ $genre }}">{{ $genre }}</a>
        </li>
    @endforeach
</ul>

<!-- Hidden Genres -->
<ul class="flex hidden-genre-list" style="position: relative; overflow: hidden; margin-top: 10px; display: none;"> <!-- Adjust margin-top as needed -->
    <!-- Display the remaining genres (initially hidden) -->
    @foreach($genres->slice(5) as $index => $genre)
        <li class="flex items-center justify-center genre-item hidden-genre-item bg-laravel" style=" color: white; border-radius: 0.25rem; padding: 0.5rem 0.75rem; margin-right: 0.5rem; font-size: 0.75rem;">
            <a href="/?genre={{ $genre }}">{{ $genre }}</a>
        </li>
    @endforeach
</ul>
