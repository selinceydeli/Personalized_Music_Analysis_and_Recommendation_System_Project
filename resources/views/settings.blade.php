<x-layout>
    <div class="settings-container bg-gradient-to-b from-red-500 to-red-300 p-8 rounded-lg mt-24">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf <!-- Add this CSRF token for security -->

            <!-- Display username at the top of the page -->
            <h2 class="text-3xl font-bold text-white mb-8">Tail Your Account {{ $data['userInfo']['username'] }}</h2>
            
            <!-- User Info Box -->
            <div class="settings-box mb-8 bg-gradient-to-b from-red-300 to-pink-200 text-white rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">User Info</h3>

                <!-- Editable fields with increased size on transparent boxes -->
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
            </div>

            <!-- Language Box -->
            <div class="settings-box mb-8 bg-gradient-to-b from-pink-200 to-red-300 text-white rounded-lg p-6">
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
            </div>

            <!-- Add dynamic classes to the body based on the selected theme -->
            <body class="{{ $data['userInfo']['theme'] === 'dark' ? 'dark-theme' : 'light-theme' }}">

            <!-- Theme Box -->
            <div class="settings-box mb-8 bg-gradient-to-b from-red-300 to-pink-200 text-white rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Theme</h3>
                <div class="mb-4">
                    <label for="theme" class="block text-sm font-medium text-gray-700">Select Theme</label>
                    <select name="theme" class="select-field">
                        <option value="light" {{ $data['userInfo']['theme'] === 'light' ? 'selected' : '' }}>Light</option>
                        <option value="dark" {{ $data['userInfo']['theme'] === 'dark' ? 'selected' : '' }}>Dark</option>
                    </select>
                </div>
            </div>

            <!-- Subscription Box -->
            <div class="settings-box mb-8 bg-gradient-to-b from-pink-200 to-red-300 text-white rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Subscription</h3>
                <p><strong>Current Subscription:</strong> {{ $data['subscription']['current'] }}</p>
                <p><strong>Rate Limit:</strong> {{ $data['subscription']['rateLimit'] }}</p>

                <!-- Show Upgrade to Premium button only if the current subscription is free -->
                @if ($data['subscription']['current'] === 'free')
                    <a href="{{ route('subscription.upgrade') }}" class="upgrade-button">
                        Upgrade to Premium
                    </a>
                @endif
            </div>

            <!-- Save Changes Button -->
            <button type="submit" class="bg-red text-black rounded py-2 px-4 hover:bg-red-600">
                Save Changes
            </button>
        </form>
    </div>

    <script>
        const themeSelector = document.querySelector('select[name="theme"]');
        const body = document.body;

        themeSelector.addEventListener('change', () => {
            const selectedTheme = themeSelector.value;

            // Remove existing theme classes
            body.classList.remove('dark-theme', 'light-theme');

            // Toggle dark mode classes based on the selected theme
            if (selectedTheme === 'dark') {
                body.classList.add('dark-theme');
            } else {
                // Toggle light mode classes based on the selected theme
                body.classList.add('light-theme');
            }
        });
    </script>
</x-layout>
