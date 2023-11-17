@props(['genresCsv'])

@php
    // Initialize an empty array for genres
    $genres = [];

    // Check if genresCsv is a JSON string and decode it
    if (is_string($genresCsv)) {
        $genres = json_decode($genresCsv);
    } elseif (is_array($genresCsv)) {
        $genres = $genresCsv;
    }

    // Now $genres contains an array of genres (either from JSON or directly passed as an array)
@endphp

<ul class="flex">
    @foreach($genres as $genre)
    <li class="flex items-center justify-center" style="background-color: red; color: white; border-radius: 0.25rem; padding: 0.5rem 1rem; margin-right: 0.5rem; font-size: 0.75rem;">
        <a href="/?genre={{ $genre }}">{{ $genre }}</a>
    </li>
    @endforeach
</ul>