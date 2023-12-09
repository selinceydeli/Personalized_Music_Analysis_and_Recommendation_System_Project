<x-layout>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Top Rated Albums by Era</div>
                    <div class="card-body">
                        <!-- Dropdown menu for selecting the era -->
                        <label for="eraSelect">Select Era:</label>
                        <select name='eraSelect' id="eraSelect" onchange="updateChart()">
                            @foreach($eras as $era)
                                <option value="{{ $era }}">{{ $era }} Eras</option>
                            @endforeach
                        </select>
                        <canvas id="albumChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var myChart;

        function updateChart() {
            var selectedEra = document.getElementById('eraSelect').value;

            // Call the backend to get updated data based on the selected era
            fetch('/analysis/favorite_albums?era=' + selectedEra)
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data)) {
                        // Extract album names, ratings, and image URLs from the JSON
                        var albumNames = data.map(album => album.name);
                        var ratings = data.map(album => album.average_rating);

                        // Update chart data based on the new data
                        myChart.data.labels = albumNames;
                        myChart.data.datasets[0].data = ratings;
                        myChart.update(); // Update the chart
                    } else {
                        console.error('Invalid data format:', data);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }


        // Wrap the chart creation inside window.onload to ensure it runs after the DOM is ready
        window.onload = function () {
            var topAlbums = {!! json_encode($topAlbums) !!};

            if (topAlbums.length > 0) {
                // Extract album names, ratings, and image URLs from the JSON
                var albumNames = topAlbums.map(album => album.name);
                var ratings = topAlbums.map(album => album.average_rating);

                // Get the canvas element
                var ctx = document.getElementById('albumChart').getContext('2d');

                // Create a bar chart
                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: albumNames,
                        datasets: [{
                            label: 'Average Rating',
                            data: ratings,
                            backgroundColor: ratings.map(rating => `rgba(255, 77, 111, ${rating / 10})`), // Dynamic color based on rating
                            borderColor: 'rgba(255, 77, 111, 1)',
                            borderWidth: 1
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
        };
    </script>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</x-layout>
