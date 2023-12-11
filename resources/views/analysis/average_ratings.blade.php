<x-layout>
<style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }

        h1 {
            font-size: 36px; /* Increased font size for the heading */
            font-weight: bold; /* Bold text */
            margin-bottom: 20px; /* Added margin below the heading */
        }

        #orderedRatingsChart {
            max-width: 100%; /* Adjusted the maximum width for the chart */
            max-height: 400px; /* Adjusted the maximum height for the chart */
        }

        /* Style for the dropdown */
        select {
            padding: 10px; /* Add padding for better appearance */
            font-size: 16px; /* Adjust the font size */
            border: 1px solid #ccc; /* Add a border */
            border-radius: 5px; /* Add border-radius for rounded corners */
            margin-top: 10px; /* Add some space between the label and the dropdown */
            cursor: pointer; /* Add a pointer cursor */
        }
    </style>

    <div class="container">
        <h1>Average Ratings for Artists</h1>

        <!-- Form to select artists and time span -->
        <form action="{{ route('analysis.average_ratings') }}" method="post">
            @csrf
            <label for="artistNames">Search Artists:</label>
            <input type="text" name="artistNames[]" id="artistNames" autocomplete="off" multiple>
            <div id="searchResults"></div>

            <button type="submit">Submit</button>
        </form>

        <!-- Display the chart -->
        <div id="chartContainer">
            <canvas id="orderedRatingsChart" width="400" height="200"></canvas>
            <div id="chartLoading" style="display: none;">Loading...</div>
        </div>
    </div>

    <script>
        var orderedRatingsChart;

        window.onload = function () {
            var orderedRatings = {!! json_encode($orderedRatings) !!};

            if (Object.keys(orderedRatings).length > 0) {
                var artists = Object.keys(orderedRatings);
                var ratings = Object.values(orderedRatings);

                var ctx = document.getElementById('orderedRatingsChart').getContext('2d');

                orderedRatingsChart = new Chart(ctx, {
                    type: 'bar',
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
                                max: 5
                            }
                        },
                        plugins: {
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function (context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed.y.toFixed(2); // Show ratings with two decimal places
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                document.getElementById('chartContainer').innerHTML = '<div>No data available</div>';
            }

            // Search functionality with debouncing
            var searchInput = document.getElementById('artistNames');
            var searchResults = document.getElementById('searchResults');
            var debounceTimer;

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    var searchQuery = searchInput.value.trim();

                    // Clear previous results
                    searchResults.innerHTML = '';

                    // Perform AJAX request to fetch matching artist names
                    if (searchQuery !== '') {
                        document.getElementById('chartLoading').style.display = 'block';

                        fetch('/average_ratings?query=' + encodeURIComponent(searchQuery))
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(artist => {
                                    var resultItem = document.createElement('div');
                                    resultItem.textContent = artist;
                                    resultItem.addEventListener('click', function () {
                                        // Adjust this part to handle an array of artist names
                                        var selectedArtists = searchInput.value.split(',');
                                        selectedArtists = selectedArtists.map(artist => artist.trim()); // Trim each artist name
                                        searchInput.value = selectedArtists.join(', ');
                                        searchResults.innerHTML = '';
                                    });
                                    searchResults.appendChild(resultItem);
                                });
                            })
                            .catch(error => console.error('Error fetching data:', error))
                            .finally(function () {
                                document.getElementById('chartLoading').style.display = 'none';
                            });
                    }
                }, 300); // Debounce time in milliseconds
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-layout>
