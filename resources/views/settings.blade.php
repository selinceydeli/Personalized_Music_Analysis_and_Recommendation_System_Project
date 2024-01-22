<x-layout>
    <x-card class="settings-container p-8 rounded-lg mt-24">
        <div class="mb-8 text-center">
            @if ($user['subscription'] === 'free')
                <a href="/plans" class="premium-button bg-laravel">Become a Premium Member</a>
            @else
                <a href="/plans" class="premium-button bg-laravel">Change Your Plan</a>
            @endif
        </div>
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf <!-- Add this CSRF token for security -->

            <!-- Display username at the top of the page because it is not changeable -->
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Tail Your Account {{ $data['userInfo']['username'] }}</h2>
            
            <!-- User Info Box -->
            <x-card class="settings-box mb-8 bg-gray-500 text-gray-800 rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">User Info</h3>

                <!-- Editable fields -->
                <div class="bg-transparent p-4 rounded-lg mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" value="{{ $data['userInfo']['name'] }}" class="input-field">
                </div>

                <div class="bg-transparent p-4 rounded-lg mb-4">
                    <label for="surname" class="block text-sm font-medium text-gray-700">Surname</label>
                    <input type="text" name="surname" value="{{ $data['userInfo']['surname'] }}" class="input-field">
                </div>

                <div class="bg-transparent p-4 rounded-lg mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="text" name="email" value="{{ $data['userInfo']['email'] }}" class="input-field mb-4">
                </div>

                <div class="bg-transparent p-4 rounded-lg mb-4">
                    <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="dob" value="{{ $data['userInfo']['dob'] }}" class="input-field">
                </div>

                <div class="bg-transparent p-4 rounded-lg mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="password" placeholder="New Password" class="input-field">
                </div>
            
            </x-card>

            <!-- Language Box -->
            <x-card class="settings-box mb-8 bg-laravel text-gray-800 rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Language</h3>
                <p><strong>Current Language:</strong> {{ $data['language']['current'] }}</p>
                <!-- Add dropdown menu for selecting a new language with increased size on transparent box -->
                <div class="bg-transparent p-4 rounded-lg mb-4">
                    <label for="language" class="block text-sm font-medium text-gray-700">Select Language</label>
                    <select name="language" class="select-field">
                        @foreach ($data['language']['options'] as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </x-card>

            <!-- Theme Box -->
            <x-card class="settings-box mb-8 bg-dark-pink text-gray-800 rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Theme</h3>
                <div class="mb-4">
                    <label for="theme" class="block text-sm font-medium text-gray-700">Select Theme</label>
                    <input type="hidden" name="theme" value="{{ $data['userInfo']['theme'] }}">
                    <div>
                        <div class="color-option pink" data-value="pink" onclick="selectTheme('pink')"></div>
                        <div class="color-option blue" data-value="blue" onclick="selectTheme('blue')"></div>
                        <div class="color-option green" data-value="green" onclick="selectTheme('green')"></div>
                        <div class="color-option yellow" data-value="yellow" onclick="selectTheme('yellow')"></div>
                        <div class="color-option red" data-value="red" onclick="selectTheme('red')"></div>
                        <div class="color-option purple" data-value="purple" onclick="selectTheme('purple')"></div>
                    </div>
                </div>
            </x-card>

            <!-- Subscription Box -->
            <x-card class="settings-box mb-8 bg-laravel text-gray-800 rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Subscription</h3>
                <p><strong>Current Subscription:</strong> {{ ucwords($data['subscription']['current']) }}</p>
                <p><strong>Rate Limit:</strong> {{ $user['rate_limit'] }}</p>
            </x-card>

            <x-card class="settings-box mb-8 bg-gray-500 text-gray-800 rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Upload Profile Image</h3>
                <div class="bg-transparent p-4 rounded-lg mb-4">
                    <form id="uploadForm" action="/api/users/{{ $data['userInfo']['username'] }}/uploadImg" method="post" enctype="multipart/form-data">
                        @csrf
                        <label for="image">Choose Image:</label>
                        <input type="file" name="image" id="image" accept="image/*" />
                        <button type="button" class="bg-laravel text-white rounded py-2 px-4" onclick="uploadFile()">Upload</button>
                    </form>
                </div>
            </x-card>

            <!-- Save Changes Button -->
            <button type="submit" class="bg-laravel text-black rounded py-2 px-4">
                Save Changes
            </button>
        </form>

        

        <script>
            function uploadFile() {
                var form = document.getElementById('uploadForm');
                var formData = new FormData(form);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', form.action, true);

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // File uploaded successfully, you can handle the response here if needed
                        window.location.href = '/user/profile/{username}'; 

                    }
                };

                xhr.send(formData);
            }
        </script>
    </x-card>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Set initial theme value
            selectTheme('{{ $data['userInfo']['theme'] }}');
        });

        function selectTheme(theme) {
            // Update hidden input value
            document.querySelector('input[name="theme"]').value = theme;

            // Remove 'selected' class from all color options
            document.querySelectorAll('.color-option').forEach(function (option) {
                option.classList.remove('selected');
            });

            // Add 'selected' class to the clicked color option
            const selectedOption = document.querySelector('.color-option[data-value="' + theme + '"]');
            selectedOption.classList.add('selected');

            // Add 'chosen' class to the chosen color option
            document.querySelectorAll('.color-option').forEach(function (option) {
                option.classList.remove('chosen');
            });
            selectedOption.classList.add('chosen');
        }
    </script>


    <style>
        .color-option {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
            cursor: pointer;
            border: 2px solid transparent; /* Add border for highlighting */
        }

        .color-option.chosen {
            border-color: #333; /* Border color for the chosen option */
        }

        .pink { background-color: #ff4d6f; }
        .blue { background-color: #007AFF; }
        .green { background-color: #00FF00; }
        .yellow { background-color: #ffff00; }
        .red { background-color: #ff0000; }
        .purple { background-color: #800080; }

        .settings-container {
            max-width: 60%; /* Half the size of the page */
            margin: 2rem auto; /* Center the container with margin on top and bottom */
            padding: 2rem; /* Padding inside the container */
            background-color: laravel; /* Fixed pink background color */
            color: #333; /* Dark grey text for better readability */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            border-radius: 0.5rem; /* Rounded corners for the container */
        }

        .settings-box {
            background-color: rgba(220, 222, 232, 0.6); /* Fixed pink background for individual boxes */
            margin-bottom: 1rem; /* Space between boxes */
            padding: 1rem; /* Padding inside boxes */
            border: none; /* Remove any borders */
            border-radius: 0.5rem; /* Rounded corners for boxes */
            
        }

        /* Style for the headers inside the settings box */
        .settings-box h3 {
            color: #333; /* Dark text for headers */
            margin-bottom: 1rem; /* Space below headers */
        }

        .input-field, .select-field {
            display: block; /* Ensure fields appear on a new line */
            width: 100%; /* Make fields take up 100% of their parent container */
            padding: 0.5rem; /* Padding inside fields */
            margin-bottom: 1rem; /* Space between each field */
            border: 1px solid #ccc; /* Light grey border for fields */
            border-radius: 0.25rem; /* Slightly rounded corners for fields */
        }

        .input-field:focus, .select-field:focus {
            border-color: black; /* Highlight with a blue border on focus */
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25); /* Subtle focus shadow */
        }

        button[type="submit"] {
            color: white; /* White text for the submit button */
            padding: 0.75rem 1.5rem; /* Padding inside the submit button */
            border-radius: 0.25rem; /* Rounded corners for the submit button */
            border: none; /* No border for the submit button */
            cursor: pointer; /* Pointer cursor for the submit button */
            display: block; /* Block display for centering */
            width: auto; /* Auto width based on content */
            margin: 1rem auto; /* Center the button horizontally */
        }

        /* Hover effect for the submit button */
        button[type="submit"]:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow effect on hover */
        }

        @media (max-width: 768px) {
            .settings-container {
                max-width: 90%; /* Adjust width for smaller screens */
            }
        }

        .premium-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            color: #fff;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .premium-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>

    <script>
        // Add JavaScript logic here
        const premiumButton = document.querySelector('.premium-button');
        premiumButton.addEventListener('click', function(event) {
            // Handle button click logic, e.g., redirecting to the premium membership page
            window.location.href = premiumButton.getAttribute('href');
        });
        premiumButton.addEventListener('mouseover', function() {
            premiumButton.style.transform = 'translateY(-2px)';
            premiumButton.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
        });
        premiumButton.addEventListener('mouseout', function() {
            premiumButton.style.transform = 'translateY(0)';
            premiumButton.style.boxShadow = 'none';
        });
    </script>
    
</x-layout>