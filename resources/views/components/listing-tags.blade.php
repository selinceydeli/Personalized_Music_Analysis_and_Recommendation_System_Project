@props(['genresCsv'])

@php
    $genres= explode('/', $genresCsv);
@endphp

<ul class="flex">
    @foreach($genres as $genre)
    <li class="flex items-center justify-center" style="background-color: red; color: white; border-radius: 0.25rem; padding: 0.5rem 1rem; margin-right: 0.5rem; font-size: 0.75rem;">
        <a href="/?genre={{ $genre }}">{{ $genre }}</a>
    </li>
    @endforeach
</ul>