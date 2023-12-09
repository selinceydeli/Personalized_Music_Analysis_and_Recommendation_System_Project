<x-layout>
    <div class="container">
        <h1>Average Ratings for Artists</h1>

        <!-- Form to select artists and time span -->
        <form action="{{ route('analysis.average_ratings') }}" method="GET">
            @csrf
            <label for="artistSearch">Search Artists:</label>
            <input type="text" name="artistSearch" id="artistSearch" autocomplete="off">
            <div id="searchResults"></div>

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

            // Search functionality
            var searchInput = document.getElementById('artistSearch');
            var searchResults = document.getElementById('searchResults');

            searchInput.addEventListener('input', function () {
                var searchQuery = this.value;

                // Clear previous results
                searchResults.innerHTML = '';

                // Perform AJAX request to fetch matching artist names
                if (searchQuery.trim() !== '') {
                    fetch('/search-artists?query=' + encodeURIComponent(searchQuery))
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(artist => {
                                var resultItem = document.createElement('div');
                                resultItem.textContent = artist;
                                resultItem.addEventListener('click', function () {
                                    searchInput.value = artist;
                                    searchResults.innerHTML = '';
                                });
                                searchResults.appendChild(resultItem);
                            });
                        })
                        .catch(error => console.error('Error fetching data:', error));
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-layout>
