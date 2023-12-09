<x-layout>
    <div class="container">
        <h1>Average Ratings for Artists</h1>

        <!-- Form to select artists and time span -->
        <form action="{{ route('analysis.average_ratings') }}" method="GET">
            @csrf
            <label for="artistNames">Select Artists:</label>
            <select name="artistNames[]" id="artistNames" multiple required>
                <!-- Populate this dropdown with the list of artists from your database -->
                @foreach($allArtists  as $artist)
                    <option value="{{ $artist }}" {{ in_array($artist, $artists) ? 'selected' : '' }}>{{ $artist }}</option>
                @endforeach
            </select>

            <label for="months">Select Time Span (Months):</label>
            <input type="number" name="months" id="months" value="6" min="1" required>

            <button type="submit">Submit</button>
        </form>

        <!-- Display the chart -->
        <canvas id="averageRatingsChart" width="400" height="200"></canvas>
    </div>

    <script>
        var averageRatingsChart;

        window.onload = function () {
            var averageRatings = {!! json_encode($averageRatings) !!};

            if (Object.keys(averageRatings).length > 0) {
                var artists = Object.keys(averageRatings);
                var ratings = Object.values(averageRatings);

                var ctx = document.getElementById('averageRatingsChart').getContext('2d');

                averageRatingsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: artists,
                        datasets: [{
                            label: 'Average Rating',
                            data: ratings,
                            fill: false,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 10 // Assuming ratings are on a scale of 0 to 10
                            }
                        }
                    }
                });
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-layout>
