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

        #dailyAverageChart {
            max-width: 80%; /* Make the chart responsive */
            max-height: 300px; /* Adjust the height as needed */
        }
    </style>

    <div class="container">
        <h1>Rhythmic Ratings: </h1>

        <canvas id="dailyAverageChart" width="400" height="200"></canvas>
    </div>

    <script>
        var dailyAverageChart;

        window.onload = function () {
            var dailyAverages = {!! json_encode($dailyAverages) !!};

            if (Object.keys(dailyAverages).length > 0) {
                var dates = Object.keys(dailyAverages);
                var averageRatings = Object.values(dailyAverages);

                var ctx = document.getElementById('dailyAverageChart').getContext('2d');

                dailyAverageChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dates.map((_, index) => index + 1),
                        datasets: [{
                            label: 'Daily Average Rating',
                            data: averageRatings,
                            fill: false,
                            borderColor: 'rgba(255, 77, 111, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                type: 'linear',
                                position: 'bottom',
                                title: {
                                    display: true,
                                    text: 'Date'
                                },
                                ticks: {
                                    stepSize: 1,
                                    callback: function (value, index) {
                                        return dates[index];
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: 5 
                            }
                        }
                    }
                });
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-layout>
