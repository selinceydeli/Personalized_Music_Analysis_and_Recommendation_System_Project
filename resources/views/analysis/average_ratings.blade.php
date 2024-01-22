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
        input {
            font-size: 16px; /* Adjust the font size */
            border: 1px solid #ccc; /* Add a border */
            border-radius: 5px; /* Add border-radius for rounded corners */
            margin-top: 10px; /* Add some space between the label and the dropdown */
        }

    </style>

    <div class="container">
        <h1>Average Ratings for Artists of Your Choice</h1>

        <!-- Form to select artists and time span -->
        <form action="{{ route('analysis.average_ratings') }}" method="post">
            @csrf
            <label for="artistNames">Search Artists:</label>
            <input type="text" name="artistNames" id="artistNames">
            <div id="searchResults"></div>
            <button type="submit" class="bg-laravel text-black rounded py-2 px-4">Submit</button>
        </form>

        <!-- Display the chart -->
        <div id="chartContainer">
            <canvas id="orderedRatingsChart" width="400" height="200"></canvas>
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

            // Function to generate random colors
            function getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            // Generate an array of random colors for each bar
            var barColors = artists.map(function () {
                return getRandomColor();
            });

            orderedRatingsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: artists,
                    datasets: [{
                        label: 'Ratings',
                        data: ratings,
                        backgroundColor: barColors,
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
                    },
                    
                }
            });
        }
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-layout>
