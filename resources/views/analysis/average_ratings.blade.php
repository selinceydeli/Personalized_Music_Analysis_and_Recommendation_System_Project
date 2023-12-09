@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Average Ratings</h1>
        
        <!-- Display performer selection dropdown and month selection -->
        <form action="{{ route('analysis.average_ratings') }}" method="get">
            @csrf
            <label for="artists">Select Performers:</label>
            <select name="artists[]" id="artists" multiple>
                @foreach($artists as $artist)
                    <option value="{{ $artist }}">{{ $artist }}</option>
                @endforeach
            </select>

            <label for="months">Select Number of Months:</label>
            <select name="months" id="months">
                @foreach($months as $month)
                    <option value="{{ $month }}">{{ $month }} Months</option>
                @endforeach
            </select>
            
            <button type="submit">Submit</button>
        </form>

        <!-- Display the average ratings for selected performers in a table -->
        <table>
            <thead>
                <tr>
                    <th>Artist</th>
                    <th>Average Rating</th>
                </tr>
            </thead>
            <tbody>
                {{-- Iterate over the performers and display average ratings --}}
            </tbody>
        </table>
    </div>
@endsection
