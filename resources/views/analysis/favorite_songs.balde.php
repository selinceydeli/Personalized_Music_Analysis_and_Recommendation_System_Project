@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Favorite Songs</h1>
        
        <!-- Display month selection dropdown -->
        <form action="{{ route('analysis.favorite_songs') }}" method="get">
            @csrf
            <label for="months">Select Number of Months:</label>
            <select name="months" id="months">
                @foreach($months as $month)
                    <option value="{{ $month }}">{{ $month }} Months</option>
                @endforeach
            </select>
            <button type="submit">Submit</button>
        </form>

        <!-- Display the top rated songs -->
        <ul>
            {{-- Iterate over the songs and display information --}}
        </ul>
    </div>
@endsection
