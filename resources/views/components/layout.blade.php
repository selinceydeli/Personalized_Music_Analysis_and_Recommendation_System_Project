<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/images/circ-logo.jpg" sizes="16x16" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-mzqFw6fK0bk5ylz5r5/gj7B5WLOw3n1l4TD9ssnR+jITFjVjU2Fz4/A5zp5s5R7xj" crossorigin="anonymous">
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.css">


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
        <a href="/"><img class="w-24" src="/images/circ-logo.jpg" alt="" class="logo" /></a>
        <ul class="flex space-x-6 mr-6 text-lg">
            @auth
                <!-- User Menu Item -->
                <li x-data="{ dashOpen: false }" class="relative">
                    <button @click="dashOpen = !dashOpen" class="hover:text-laravel flex items-center">
                        <i class="fa-solid fa-user"></i>&nbsp;Welcome {{ auth()->user()->name }}
                    </button>                    
                    <div x-show="dashOpen" @click.away="dashOpen = false" class="absolute mt-2 w-48 rounded-md shadow-lg bg-white z-50" style="display: none">
                        <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                        <form class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" method="POST" action="/logout">
                            @csrf
                            <button type="submit">
                                <i></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
                <!-- Recommendations Menu Item -->
                <li x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="hover:text-laravel flex items-center">
                        <i class="fa-solid fa-music"></i>&nbsp;Recommendations
                    </button>                    
                    <div x-show="open" @click.away="open = false" class="absolute mt-2 w-48 rounded-md shadow-lg bg-white z-50" style="display: none">
                        <a href="{{ route('dashboard.genretaste') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Genre Taste</a>
                        <a href="{{ route('dashboard.energy') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Energy & Dance Vibes</a>
                        <a href="{{ route('dashboard.negativevalence') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Moody Mix</a>
                        <a href="{{ route('dashboard.positivevalence') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Positive Mix</a>
                    </div>
                </li>
                <!-- Analysis Menu Item -->
                <li x-data="{ analysisOpen: false }" class="relative">
                    <button @click="analysisOpen = !analysisOpen" class="hover:text-laravel flex items-center">
                        <i class="fas fa-chart-bar"></i>&nbsp;Analysis
                    </button>
                    <div x-show="analysisOpen" @click.away="analysisOpen = false" class="absolute mt-2 w-48 rounded-md shadow-lg bg-white z-50" style="display: none">
                        <a href="{{ route('analysis.favorite_albums') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Favorite Albums</a>
                        <a href="{{ route('analysis.favorite_songs') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Favorite Songs</a>
                        <a href="{{ route('analysis.average_ratings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Average Ratings</a>
                        <a href="{{ route('analysis.daily_average') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Daily Average</a>
                    </div>
                </li>
                <li>
                    <a href="/add" class="hover:text-laravel"><i class="fa-solid fa-plus"></i>
                        Upload Music</a>
                </li>
                <li>
                    <a href="/downloads" class="hover:text-laravel">
                        <i class="fa-solid fa-download"></i> Download Music
                    </a>
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
        @yield('content') <!-- This is where the specific content of your views will be inserted -->
    </main>
    {{ $slot }}

    
    <x-flash-message />
    <script src="{{ asset('js/password-checklist.js') }}"></script>
    <script src="{{ asset('js/password-toggle.js') }}"></script>
    <script src="{{ asset('js/password-toggle-login.js') }}"></script>

</body>

</html>
