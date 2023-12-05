<!-- resources/views/analysis/favorite_albums.blade.php -->

@extends('components.layout')

@section('content')
    <h1>Favorite Albums Analysis</h1>

    <form action="{{ route('analysis.favorite_albums') }}" method="get">
        <label for="era">Select Era:</label>
        <select name="era" id="era">
            @foreach ($eras as $era)
                <option value="{{ $era }}">{{ $era }}</option>
            @endforeach
        </select>
        <button type="submit">Get Favorite Albums</button>
    </form>

    {{-- Display the fetched favorite albums here --}}
    {{-- You can use Vue.js or Livewire to make this section dynamic --}}
@endsection
