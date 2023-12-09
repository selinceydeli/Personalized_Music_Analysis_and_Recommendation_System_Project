<x-layout>
    <div class="container">
        <h1>Favorite Songs</h1>

        <!-- Display month selection dropdown -->
        <label for="monthSelect">Select Number of Months:</label>
        <select name="monthSelect" id="monthSelect" onchange="updateChart()">
            @foreach($monthArray as $months => $label)
                <option value="{{ $months }}">{{ $label }}</option>
            @endforeach
        </select>
        <canvas id="songChart" width="400" height="200"></canvas>
    </div>

    <script>
        var myChart;

        function updateChart() {
            var selectedMonths = document.getElementById('monthSelect').value;

            // Call the backend to get updated data based on the selected months
            fetch('/analysis/favorite_songs?months=' + selectedMonths)
                .then(response => response.json())
                .then(topSongs => {
                    if (Array.isArray(topSongs)) {
                        // Extract song names, ratings, and other relevant data from the JSON
                        var songNames = topSongs.map(song => song.name);
                        var ratings = topSongs.map(song => song.average_rating);

                        myChart.data.labels = songNames;
                        myChart.data.datasets[0].data = ratings;
                        myChart.update();
                    } else {
                        console.error('Invalid data format:', topSongs);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        window.onload = function () {
            var topSongs = {!! json_encode($topSongs) !!};

            if (topSongs.length > 0) {
                var songNames = topSongs.map(song => song.name);
                var ratings = topSongs.map(song => song.average_rating);

                var ctx = document.getElementById('songChart').getContext('2d');

                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: songNames,
                        datasets: [{
                            label: 'Average Rating',
                            data: ratings,
                            backgroundColor: ratings.map(rating => `rgba(255, 77, 111, ${rating / 10})`),
                            borderColor: 'rgba(255, 77, 111, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 5
                            }
                        },
                        onClick: function (event, elements) {
                            if (elements.length > 0) {
                                // Get the index of the clicked bar
                                var clickedIndex = elements[0].index;

                                // Assuming you have a URL for each song, replace 'your_song_url_array' with your actual array of URLs
                                var songUrl = your_song_url_array[clickedIndex];

                                // Redirect the user to the song page
                                window.location.href = songUrl;
                            }
                        }
                    }
                });
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-layout>
