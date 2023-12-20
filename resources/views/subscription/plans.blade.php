<x-layout>
    <x-card class="relative">
        <!-- Main headline -->
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">
            Elevate Your Experience with Premium Plans</h1>

        <!-- Plans Section -->
        <div class="flex justify-center gap-8">
            <!-- Free Plan -->
            <div class="plan-container">
                <div class="plan-card bg-blue-300 p-6 rounded-md shadow-md cursor-pointer" onclick="showPopup('free')">
                    <h2 class="text-xl font-bold text-gray-800 mb-2 text-center">Free</h2>
                    <div class="flex justify-center items-center mb-4">
                        <!-- Free Icon -->
                        <i class="fas fa-medal text-blue-600 text-5xl mb-2"></i>
                    </div>
                    <p class="text-gray-600 text-center">Basic Access</p>
                </div>
                <p class="text-center mt-2 text-lg text-gray-600">Just Getting Started</p>
                @if ($user['subscription'] === 'free')
                    <p class="text-center mt-2 text-s text-gray-600">Current Plan</p>
                @endif
            </div>

            <!-- Silver Plan -->
            <div class="plan-container">
                <div class="plan-card bg-silver p-6 rounded-md shadow-md cursor-pointer silver-shine relative"
                    onclick="showPopup('silver')">
                    <h2 class="text-xl font-bold text-gray-800 mb-2 text-center">Silver</h2>
                    <div class="flex justify-center items-center mb-4">
                        <!-- Silver Icon -->
                        <i class="fas fa-bolt text-gray-600 text-5xl mb-2"></i>
                    </div>
                    <p class="text-gray-600 text-center">Enhanced Access</p>
                </div>
                <p class="text-center mt-2 text-lg text-gray-600">Unlock More Vibes</p>
                @if ($user['subscription'] === 'silver')
                    <p class="text-center mt-2 text-s text-gray-600">Currently Enrolled</p>
                @endif

                <!-- Silver Upgrade Button -->
                <div class="upgrade-button-container text-center mt-4">
                    <button class="upgrade-button bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                        Upgrade to Silver
                    </button>
                </div>
            </div>

            <!-- Gold Plan -->
            <div class="plan-container">
                <div class="plan-card bg-gold p-6 rounded-md shadow-md cursor-pointer gold-shine relative"
                    onclick="showPopup('gold')">
                    <h2 class="text-xl font-bold text-gray-800 mb-2 text-center">Gold</h2>
                    <div class="flex justify-center items-center mb-4">
                        <!-- Gold Icon -->
                        <i class="fas fa-crown text-5xl text-yellow-500"></i>
                    </div>
                    <p class="text-gray-600 text-center">Premium Access</p>
                </div>
                @if ($user['subscription'] === 'gold')
                    <p class="text-center mt-2 text-s text-gray-600">Currently Enrolled</p>
                @endif
                <p class="text-center mt-2 text-lg text-gray-600">For the Real Music Lovers</p>
                <div class="flex items-center justify-center mt-2">
                    <p class="text-s text-gray-600 ml-1">Highly Recommended</p>
                    <span class="text-xl text-yellow-500">🔥</span>
                </div>

                <div class="upgrade-button-container text-center mt-4">
                    <button
                        class="upgrade-button-gold bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded flex items-center justify-center">
                        Upgrade to Gold
                    </button>
                </div>
            </div>
        </div>

        <!-- Silver Plan Popup -->
        <div id="silver-popup" class="popup-container hidden">
            <div class="popup-content bg-white p-8 rounded-md shadow-md">
                <h2 class="text-3xl font-bold text-gray-800 mb-4 text-center">Silver Plan</h2>
                <ul class="text-sm text-gray-700 pl-5 list-disc">
                    <li class="mb-3">
                        <strong>Playlist Management:</strong> Create, edit, and organize personal playlists.
                    </li>
                    <li class="mb-3">
                        <strong>Sound Equalizer Settings:</strong> Customize sound profiles for different music genres.
                    </li>
                    <li class="mb-3">
                        <strong>Exclusive Radio Stations:</strong> Access to a variety of genre-specific online radio
                        stations.
                    </li>
                    <li class="mb-3">
                        <strong>Offline Mode:</strong> Download a limited number of songs for offline listening.
                    </li>
                </ul>
            </div>
        </div>


        <!-- Gold Plan Popup -->
        <div id="gold-popup" class="popup-container hidden">
            <div class="popup-content bg-white p-8 rounded-md shadow-md">
                <h2 class="text-3xl font-bold text-gray-800 mb-4 text-center">Gold Plan</h2>
                <ul class="text-sm text-gray-700 pl-5 list-disc">
                    <li class="mb-3">
                        <strong>All Silver Features:</strong> Includes everything in the Silver package.
                    </li>
                    <li class="mb-3">
                        <strong>Advanced Playlist Collaboration:</strong> Share and collaborate on playlists with
                        friends or the MusicTailor community.
                    </li>
                    <li class="mb-3">
                        <strong>Unlimited Offline Access:</strong> Download an unlimited number of songs for offline
                        playback.
                    </li>
                    <li class="mb-3">
                        <strong>Concert and Event Alerts:</strong> Notifications about concerts and events based on user
                        preferences.
                    </li>
                </ul>
            </div>
        </div>
    </x-card>

    <script>
        // Function to show the respective popup below the plan
        let silverPopupVisible = false;

        function showPopup(plan) {
            const popup = document.getElementById(`${plan}-popup`);
            if (popup.style.display === 'block') {
                popup.style.display = 'none';
            } else {
                popup.style.display = 'block';
                const planContainer = document.getElementById(`${plan}-container`);
                popup.style.position = 'absolute';
                popup.style.top = `${planContainer.offsetTop + planContainer.offsetHeight}px`;
                popup.style.left = `${planContainer.offsetLeft}px`;
            }
        }
    </script>

    <style>
        .upgrade-button {
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Adding a subtle shadow */
        }

        .upgrade-button-gold {
            padding: 10px 20px;
            /* Adjust padding */
            margin-top: 10px;
            /* Adjust margin top */
            margin-left: 50px;
            font-size: 16px;
            /* Adjust font size */
            border: none;
            color: #fff;
            /* Text color */
            border-radius: 5px;
            /* Rounded corners */
            transition: background-color 0.3s ease;
            /* Smooth transition */
        }

        .upgrade-button:hover {
            /* Change color on hover to match the Silver plan */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Enhance shadow on hover */
            cursor: pointer;
        }

        /* Adjust the icon color as necessary */
        .upgrade-button i {
            font-size: 1.2rem;
        }

        .popup-container {
            position: absolute;
            display: none;
            bottom: calc(100% + 10px);
            /* Adjust the distance between plan and popup */
            left: 50%;
            transform: translateX(-50%);
        }

        #silver-popup {
            top: 420px;
            /* Adjust the distance from the top */
            left: 730px;
            /* Adjust the distance from the left */
        }

        #gold-popup {
            top: 420px;
            /* Adjust the distance from the top */
            left: 1100px;
            /* Adjust the distance from the left */
        }

        .plan-container {
            position: relative;
        }

        .popup-content {
            max-width: 350px;
            /* Adjust width as needed */
            background-color: #f9fafb;
            /* Light background color */
            border-radius: 10px;
            /* Rounded corners */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
        }

        .popup-content h2 {
            color: #333;
            /* Darker title color */
        }

        .popup-content ul {
            padding-left: 20px;
            /* Indent the list */
        }

        .popup-content li {
            color: #555;
            /* Text color */
            line-height: 1.6;
            /* Adjust line spacing */
        }


        /* Add this CSS within your Blade file or in the style section of your layout file */
        .plan-card {
            width: 280px;
            max-width: 100%;
            transition: transform 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            filter: brightness(110%);
        }

        .bg-silver {
            background-color: #C0C0C0;
            /* Silver color */
        }

        .bg-gold {
            background-color: #FFD700;
            /* Gold color */
        }

        @keyframes silverShine {
            0% {
                background-color: #c0c0c0bd;
                /* Initial Silver */
            }

            50% {
                background-color: #e0e0e0;
                /* Intermediate Color */
            }

            100% {
                background-color: #c0c0c0d0;
                /* Vivid Silver */
            }
        }


        .silver-shine {
            animation: silverShine 2s infinite;
        }

        /* Gold Shining Animation */
        @keyframes goldTransition {
            0% {
                background-color: #FFD700;
            }

            /* Vivid Gold */
            50% {
                background-color: #EAC117;
            }

            /* Transition Color */
            100% {
                background-color: #FFD700;
            }

            /* Back to Vivid Gold */
        }

        .gold-shine {
            animation: goldTransition 2s infinite;
        }

        .text-gold {
            color: #ff0000e3;
            /* Vivid Gold */
        }

        .plan-card {
            width: 280px;
            height: 175px; /* Set a fixed height for all cards */
            max-width: 100%;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 6px; /* Your existing padding */
            border-radius: 10px; /* Your existing border radius */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Your existing box shadow */
            cursor: pointer; /* Your existing cursor property */
        }

        /* Now find your .upgrade-button-container class or add this new style if it doesn't exist */
        .upgrade-button-container {
            margin-top: auto; /* This will push the button to the bottom */
        }
    </style>
</x-layout>
