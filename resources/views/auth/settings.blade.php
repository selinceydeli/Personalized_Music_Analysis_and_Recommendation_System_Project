<!-- resources/views/settings.blade.php -->

<x-layout>
    <div class="settings-container bg-gradient-to-b from-red-500 to-pink-400 p-8 rounded-lg mt-24">

        <!-- User Info Box -->
        <div class="settings-box mb-8">
            <h3 class="text-2xl font-bold mb-4">User Info</h3>
            <p><strong>Username:</strong> {{ $data['userInfo']['username'] }}</p>
            <p><strong>Date of Birth:</strong> {{ $data['userInfo']['dob'] }}</p>

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
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="password" placeholder="New Password" class="input-field">
            </div>
        </div>

        <!-- Language Box -->
        <div class="settings-box mb-8">
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

        <!-- Subscription Box -->
        <div class="settings-box mb-8">
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
        <button type="submit" class="bg-laravel text-black rounded py-2 px-4 hover:bg-laravel-600">
            Save Changes
        </button>
    </div>
</x-layout>
