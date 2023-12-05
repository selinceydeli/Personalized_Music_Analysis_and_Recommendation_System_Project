<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="images/circ-logo.jpg" sizes="16x16" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-mzqFw6fK0bk5ylz5r5/gj7B5WLOw3n1l4TD9ssnR+jITFjVjU2Fz4/A5zp5s5R7xj" crossorigin="anonymous">
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        laravel: "#ff4d6f",
                    },
                },
            },
        };
    </script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Music Tailor | Find Music that Suits You</title>
</head>

<body class="mb-48">
    <nav class="flex justify-between items-center mb-4">
        <a href="/"><img class="w-24" src="images/circ-logo.jpg" alt="" class="logo" /></a>
        <ul class="flex space-x-6 mr-6 text-lg">
            @auth
                <li>
                    <span class="font-bold uppercase">
                        Welcome {{ auth()->user()->name }}
                    </span>
                </li>
                <!-- Recommendations Menu Item -->
                <li x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="hover:text-laravel flex items-center">
                        <i class="fa-solid fa-music"></i>&nbsp;Recommendations
                    </button>                    
                    <div x-show="open" @click.away="open = false" class="absolute mt-2 w-48 rounded-md shadow-lg bg-white z-50">
                        <a href="{{ route('dashboard.genretaste') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Genre Taste</a>
                        <a href="{{ route('dashboard.energy') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Energy & Dance Vibes</a>
                    </div>
                </li>
                <!-- Analysis -->
                <li x-data="{ dashboardOpen: false }" class="relative">
                    <button @click="dashboardOpen = !dashboardOpen" class="hover:text-laravel flex items-center">
                        <i class="fas fa-chart-bar"></i>&nbsp;Analysis
                    </button>
                    <div x-show="dashboardOpen" @click.away="dashboardOpen = false" class="absolute mt-2 w-48 rounded-md shadow-lg bg-white z-50">
                        <a href="{{ route('dashboard.top-songs') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Top Songs</a>
                        <a href="{{ route('dashboard.top-albums') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Top Albums</a>
                        <a href="{{ route('dashboard.average-ratings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Average Ratings</a>
                    </div>
                </li>
                <li>
                    <a href="/add" class="hover:text-laravel"><i class="fa-solid fa-plus"></i>
                        Upload Music</a>
                </li>
                <li>
                    <button id="dashboard-menu-button" class="text-gray-500 hover:text-gray-900 ml-4"
                        onclick="toggleDashboardMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </li>
            @else
                <li>
                    <a href="/register" class="hover:text-laravel"><i class="fa-solid fa-user-plus"></i> Register</a>
                </li>
                <li>
                    <a href="/login" class="hover:text-laravel"><i class="fa-solid fa-arrow-right-to-bracket"></i>
                        Login</a>
                </li>
            @endauth
        </ul>
    </nav>
    <main>
        {{ $slot }}
    </main>
    <footer
        class="fixed bottom-0 left-0 w-full flex items-center justify-start font-bold bg-laravel text-white h-24 mt-24 opacity-90 md:justify-center">
        <p class="ml-2">Copyright &copy; 2023, All Rights reserved</p>

        <a href="/add" class="absolute top-1/3 right-10 bg-black text-white py-2 px-5">Upload Music</a>
    </footer>
    <x-flash-message />
    <script src="{{ asset('js/password-checklist.js') }}"></script>
    <script src="{{ asset('js/password-toggle.js') }}"></script>
    <script src="{{ asset('js/password-toggle-login.js') }}"></script>

    <div id="dashboard-menu" class="hidden fixed top-16 right-4 bg-white p-4 border rounded-lg shadow-lg">
        <ul>
            <li><a href="/playlists"><i class="fas fa-list"></i> Playlists</a></li>
            <li><a href="/settings"><i class="fas fa-cog"></i> Settings</a></li>
            <li>
                <form class="inline" method="POST" action="/logout">
                    @csrf
                    <button type="submit">
                        <i class="fas fa-door-closed"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>


    <script>
        var dashboardMenuVisible = false;
        var dashboardMenu = document.getElementById('dashboard-menu');

        function toggleDashboardMenu() {
            dashboardMenuVisible = !dashboardMenuVisible;
            dashboardMenu.style.display = dashboardMenuVisible ? 'block' : 'none';
        }

        // Close the dashboard menu if the user clicks outside of it
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#dashboard-menu-button') && !event.target.closest('#dashboard-menu')) {
                dashboardMenu.style.display = 'none';
                dashboardMenuVisible = false;
            }
        });
    </script>
</body>

</html>
