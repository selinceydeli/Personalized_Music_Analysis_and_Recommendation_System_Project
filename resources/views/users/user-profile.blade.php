@if (!function_exists('formatSongDuration'))
    @php
        function formatSongDuration($milliseconds)
        {
            $seconds = $milliseconds / 1000; // Convert milliseconds to seconds
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;

            if ($hours > 0) {
                return sprintf('%dh : %dm : %02ds', $hours, $remainingMinutes, $remainingSeconds);
            } else {
                return sprintf('%dm : %02ds', $minutes, $remainingSeconds);
            }
        }
    @endphp
@endif


@if (!function_exists('formatDate'))
    @php
        // Helper function to format date as "Added at Day:Month:Year"
        function formatDate($date)
        {
            return 'Created at ' . date('d M Y', strtotime($date));
        }
    @endphp
@endif



<x-layout>
    
    <!-- Title Section -->
    <div class="title-section text-center py-4 bg-black text-white">
        <h1 class="text-4xl font-bold">{{ $user->name }}'s Public Music Tailor Profile</h1>
    </div>

    <x-card class="profile-container rounded-lg mt-4 mx-auto">
        <!-- Profile Image Section -->
        <div class="profile-image-section text-center bg-pink-500 py-12">
            <!-- Use a div to create a larger space for the profile image -->
            <div
                class="profile-picture bg-white m-auto rounded-full w-48 h-48 flex items-center justify-center overflow-hidden border-4 border-white">
                <img src="{{ $user->image }}" alt="Profile Picture" class="w-full h-full object-cover rounded-full">
            </div>
        </div>

        <!-- User Name Section -->
        <div class="user-name-section text-center mb-2">
            <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }} {{ $user->surname }}</h2>
        </div>
    </x-card>
    <!-- Stories Section -->
    <div class="stories-section text-center bg-pink-100 rounded-lg overflow-hidden shadow-lg py-12 mt-4 mx-auto">
        <h2 class="text-2xl font-bold mb-4 text-center">Music Tailor Wrapped</h2>

        <!-- Favorite Genre -->
        <div class="story-card rounded-lg overflow-hidden shadow-lg mb-4">
            <h3 class="text-lg font-semibold mb-2">Favorite Genre</h3>
            <div class="profile-image-section text-center bg-pink-500 py-12">
                <p class="text-gray-700">Your Favorite Genre</p>
        </div>
        </div>

        <!-- Top 5 Albums of all time -->
        <div class="story-card rounded-lg overflow-hidden shadow-lg mb-4">
            <div class="col-span-1">
                <h3 class="text-lg font-semibold mb-2">Top 5 Albums of all time</h3>
                <div class="profile-image-section flex items-center justify-center bg-pink-500 py-12">
                    <div class="ml-4">

                        @foreach ($top5Albums as $album)
                            <p>Album Name: {{ $album->name }}</p>
                            <p>Average Rating: {{ number_format($album->average_rating, 2) }}</p>
                            <hr>
                        @endforeach
                        </div>
                    </div>
                </div>
        </div>

        <!-- Song of the Year -->
        <div class="story-card rounded-lg overflow-hidden shadow-lg mb-4">
            <div class="col-span-1">
                <h3 class="text-lg font-semibold mb-2">Top Song of the Year</h3>
                <div class="profile-image-section flex items-center justify-center bg-pink-500 py-12">
                    <div class="ml-4">
                    {{-- Display top song of the year --}}
                    @foreach ($songOfYear as $song)
                        <p>Name: {{ $song->name }}</p>
                        <p>Duration: {{ $song->duration }}</p>
                        <p>Tempo: {{ $song->tempo }}</p>
                        <p>Average Rating: {{ number_format($song->average_rating, 2) }}</p>
                        <hr>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Playlists Section -->
    <div class="playlists-section w-full p-6 bg-white">
        <h2 class="text-2xl font-bold mb-4 text-center">
            @if ($user->username == auth()->user()->username)
                My Playlists
            @else
                {{ $user->username }}'s Playlists
            @endif
        </h2>
        <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
            @foreach ($playlists as $playlist)
                <x-card class="relative">
                    <div class="flex">
                        <img class="w-48 h-48 mr-6 md:block"
                            src="{{ $playlist['image_url'] ? $playlist['image_url'] : asset('/images/playlist.png') }}"
                            alt="Playlist Image" />
                        <div class="text-lg mt-4">
                            <a href="/playlist/{{ $playlist->id }}">
                                <strong>{{ $playlist['playlist_name'] }}</strong>
                            </a>
                            <!-- Date -->
                            <div class="text-lg mt-12">
                                <i class="fas fa-calendar"></i>
                                <strong>{{ formatDate($playlist['created_at']) }}</strong>
                                <!-- Display the publishing date -->
                            </div>
                            <!-- Display album performers -->
                            <div class="mt-2">
                                <p class="mt-12">
                                    <i class="fa-solid fa-user"></i> <!-- Microphone icon -->
                                    @php
                                        $usersCount = $playlist->users->count();
                                        $displayCount = min(2, $usersCount);
                                        $remainingUsers = max(0, $usersCount - $displayCount);
                                    @endphp

                                    @foreach ($playlist->users->take($displayCount) as $index => $us)
                                        @if ($us->username === $user->username)
                                            <span class="text-lg font-bold"> {{ $us->username }}</span>
                                        @else
                                            <a href="/user/profile/{{ $us->username }}" class="text-lg font-bold">
                                                {{ $us->username }}
                                            </a>
                                        @endif

                                        @if ($index < $displayCount - 1)
                                            , <!-- Add a comma if it's not the last displayed performer -->
                                        @endif
                                    @endforeach

                                    @if ($remainingUsers > 0)
                                        @if ($displayCount < $usersCount)
                                            <!-- Show an indication that there are more users -->
                                            and {{ $remainingUsers }} more
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
</x-layout>

<style>
    .profile-container {
        max-width: 600px;
        /* Adjust the size as needed */
        background-color: #fff;
        /* Assuming a white background for the card */
        box-shadow: 0 10px 25px 0 rgba(0, 0, 0, 0.1);
        /* Shadow for depth */
        border-radius: 10px;
        /* Rounded corners for the card */
        margin-bottom: 2rem; /* Space below the image section */
    }

    .profile-image-section {
        background-color: #FCE4EC;
        /* Adjust the pink background shade as needed */
        margin-bottom: 1rem;
        /* Space below the image section */
        padding: 50px; /* Add padding to the story card */
    }

    .profile-picture {
        width: 200px;
        /* Increased width for the profile image */
        height: 200px;
        /* Increased height for the profile image */
    }

    .title-section {
        background-color: #222;
        /* Dark background for the title */
        padding: 1rem 0;
        /* Padding above and below the title */
    }

    .playlists-section {
        max-width: 100%;
        /* Full width for the playlist section */
        margin: auto;
    }

    .playlist-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        /* Smooth transition for hover effects */
        cursor: pointer;
        /* Change cursor on hover */
    }

    .playlist-card:hover {
        transform: translateY(-5px);
        /* Slight lift effect on hover */
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        /* Enhanced shadow on hover */
    }
    .stories-section {
        display: flex;
        flex-direction: column;
        max-width: 70%;
        margin: auto;
        align-items: center;
    }
    .story-card {
        margin-bottom: 1rem; /* Space below the image section */
        padding: 20px; /* Add padding to the story card */
        align-items: center;
        background-color: #FFF; 
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* Smooth transition for hover effects */
    }
    .playlist-card:hover {
        transform: translateY(-5px); /* Slight lift effect on hover */
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); /* Enhanced shadow on hover */
    }
    .stories-section {
        display: flex;
        flex-direction: column;
        max-width: 70%;
        margin: auto;
        align-items: center;
        padding: 20px; /* Add padding to the story card */
    }
    .story-card {
        margin-bottom: 1rem; /* Space below the image section */
        align-items: center;
        background-color: #FFF; 
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* Smooth transition for hover effects */
    }
    .story-card:hover {
        transform: translateY(-5px); /* Slight lift effect on hover */
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); /* Enhanced shadow on hover */
    }
</style>
