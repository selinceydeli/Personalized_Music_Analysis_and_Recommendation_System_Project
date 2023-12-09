<x-layout>
    <div class="container">
        <h1>Daily Average Ratings</h1>

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
                            borderColor: 'rgba(75, 192, 192, 1)',
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
