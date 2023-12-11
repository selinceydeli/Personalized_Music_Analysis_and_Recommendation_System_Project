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

        #songChart {
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
        <h1>Harmonious Highlights: Explore the Top-Tuned Treasures</h1>

        <form method=post action="{{ route('analysis.favorite_songs') }}">
            @csrf

        <!-- Display month selection dropdown -->
        <label for="monthSelect">Select Time Span:</label>
        <select name="monthSelect" id="monthSelect" onchange="updateChart()">
            @foreach($monthArray as $month)
                <option value="{{ $month }}" @if($month === $monthSelect) selected @endif>{{ $month }} Months</option>
            @endforeach
        </select>
        <button type="submit">Submit</button>
    </form>
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
                        myChart.data.datasets[0].backgroundColor = generateRandomColors(topSongs.length);
                        myChart.update();
                    } else {
                        console.error('Invalid data format:', topSongs);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Function to generate random colors
        function generateRandomColors(numColors) {
            var colors = [];
            for (var i = 0; i < numColors; i++) {
                colors.push(getRandomColor());
            }
            return colors;
        }

        // Function to get a random color
        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        window.onload = function () {
            var topSongs = {!! json_encode($topSongs) !!};

            if (topSongs.length > 0) {
                var songNames = topSongs.map(song => song.name);
                var ratings = topSongs.map(song => song.average_rating);

                var ctx = document.getElementById('songChart').getContext('2d');

                myChart = new Chart(ctx, {
                    type: 'pie', 
                    data: {
                        labels: songNames,
                        datasets: [{
                            data: ratings,
                            backgroundColor: generateRandomColors(topSongs.length), // Set initial random colors
                            borderColor: 'white',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        onClick: function (event, elements) {
                            if (elements.length > 0) {
                                // Get the index of the clicked pie
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
