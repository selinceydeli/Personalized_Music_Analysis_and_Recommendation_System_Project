<x-layout>
    <!-- Title Section -->
    <div class="title-section text-center py-4 bg-black text-white">
        <h1 class="text-4xl font-bold">{{ $user->name }}'s Public Music Tailor Profile</h1>
    </div>

    <x-card class="profile-container rounded-lg mt-4 mx-auto">
        <!-- Profile Image Section -->
        <div class="profile-image-section text-center bg-pink-500 py-12">
            <!-- Use a div to create a larger space for the profile image -->
            <div class="profile-picture bg-white m-auto rounded-full w-48 h-48 flex items-center justify-center overflow-hidden border-4 border-white">
                <img src="{{ $user->image }}" alt="Profile Picture" class="w-full h-full object-cover rounded-full">
            </div>
        </div>

        <!-- User Name Section -->
        <div class="user-name-section text-center mb-2">
            <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }} {{ $user->surname }}</h2>
        </div>
    </x-card>
    <!-- Stories Section -->
    <div class="playlists-section w-full p-6 bg-white">
        <h2 class="text-2xl font-bold mb-4 text-center">Your Stories</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        </div>
    </div>
    <!-- Playlists Section -->
    <div class="playlists-section w-full p-6 bg-white">
        <h2 class="text-2xl font-bold mb-4 text-center">Your Playlists</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        </div>
    </div>
</x-layout>

<style>
    .profile-container {
        max-width: 600px; /* Adjust the size as needed */
        background-color: #fff; /* Assuming a white background for the card */
        box-shadow: 0 10px 25px 0 rgba(0, 0, 0, 0.1); /* Shadow for depth */
        border-radius: 10px; /* Rounded corners for the card */
    }
    .profile-image-section {
        background-color: #FCE4EC; /* Adjust the pink background shade as needed */
        margin-bottom: 1rem; /* Space below the image section */
    }
    .profile-picture {
        width: 200px; /* Increased width for the profile image */
        height: 200px; /* Increased height for the profile image */
    }
    .title-section {
        background-color: #222; /* Dark background for the title */
        padding: 1rem 0; /* Padding above and below the title */
    }
    .playlists-section {
        max-width: 100%; /* Full width for the playlist section */
        margin: auto;
    }
    .playlist-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* Smooth transition for hover effects */
        cursor: pointer; /* Change cursor on hover */
    }
    .playlist-card:hover {
        transform: translateY(-5px); /* Slight lift effect on hover */
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); /* Enhanced shadow on hover */
    }
</style>