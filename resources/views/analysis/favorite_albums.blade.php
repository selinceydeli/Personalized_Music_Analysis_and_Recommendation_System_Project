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

        #albumChart {
            max-width: 90%; /* Make the chart responsive */
            max-height: 300px; /* Adjust the height as needed */
        }

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
        <h1>Greatest Albums of All Time, Tailored by Your Ratings</h1>
        <form method=post action="{{ route('analysis.favorite_albums') }}">
            @csrf
            <!-- Dropdown menu for selecting the era -->
            <label for="eraSelect">Select Era:</label>
            <select name='eraSelect' id="eraSelect" onchange="updateChart()">
                @foreach($eras as $era)
                    <option value="{{ $era }}" @if($era === $eraSelect) selected @endif>{{ $era }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-laravel text-black rounded py-2 px-4">Submit</button>
    </form>
        <canvas id="albumChart"></canvas>
    </div>

    <script>
        var myChart;

        function updateChart() {

            
            var selectedEra = document.getElementById('eraSelect').value;

            // Call the backend to get updated data based on the selected era
            fetch('/analysis/favorite_albums?era=' + selectedEra)
                .then(response => response.json())
                .then(topAlbums => {
                    if (Array.isArray(topAlbums)) {
                        // Extract album names, ratings, and image URLs from the JSON
                        var albumNames = topAlbums.map(album => album.name);
                        var ratings = topAlbums.map(album => album.average_rating);

                        // Update chart data based on the new data
                        myChart.data.labels = albumNames;
                        myChart.data.datasets[0].data = ratings;
                        myChart.data.datasets[0].backgroundColor = generateRandomColors(data.length); // Dynamic colors
                        myChart.update(); // Update the chart
                    } else {
                        console.error('Invalid data format:', topAlbums);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Function to generate random vibrant colors
        function generateRandomColors(numColors) {
            var colors = [];
            for (var i = 0; i < numColors; i++) {
                colors.push(getRandomVibrantColor());
            }
            return colors;
        }

        // Function to get a random vibrant color
        function getRandomVibrantColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
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
                            data: ratings,
                            backgroundColor: generateRandomColors(topAlbums.length),
                            borderColor: 'white',
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
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    generateLabels: function (chart) {
                                        var data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map(function (label, i) {
                                                var dataset = data.datasets[0];
                                                var backgroundColor = dataset.backgroundColor[i] || 'rgba(0,0,0,0)';
                                                return {
                                                    text: label,
                                                    fillStyle: backgroundColor,
                                                    strokeStyle: backgroundColor,
                                                    lineWidth: 2,
                                                    hidden: isNaN(dataset.data[i]) || dataset.data[i] === null,
                                                    index: i
                                                };
                                            });
                                        }
                                        return [];
                                    }
                                }
                            }
                        }
                    }
                });
            }
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-layout>
