<x-layout>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Top Rated Albums by Era</div>
                    <div class="card-body">
                        <!-- Dropdown menu for selecting the era -->
                        <label for="eraSelect">Select Era:</label>
                        <select id="eraSelect" onchange="updateChart()">
                            <option value="50s">50s</option>
                            <option value="60s">60s</option>
                            <option value="70s">70s</option>
                            <option value="80s">80s</option>
                            <option value="90s">90s</option>
                            <option value="20s">2000s</option>
                        </select>
                        <canvas id="albumChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        
        // Assuming $topAlbums is the JSON data passed from your controller
        var topAlbums = {!! json_encode($topAlbums) !!};

        // Extract album names, ratings, and image URLs from the JSON
        var albumNames = topAlbums.map(albums => albums.name);
        var ratings = topAlbums.map(albums => albums.average_rating);

        // Get the canvas element
        var ctx = document.getElementById('albumChart').getContext('2d');

        // Create a bar chart
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: albumNames,
                datasets: [{
                    label: 'Average Rating',
                    data: ratings,
                    backgroundColor: ratings.map(rating => `rgba(75, 192, 192, ${rating / 10})`), // Dynamic color based on rating
                    borderColor: 'rgba(75, 192, 192, 1)',
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
</script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</x-layout>
