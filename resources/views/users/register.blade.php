<!-- resources/views/register.blade.php -->

<x-layout>
    <x-card class="p-10 rounded max-w-lg mx-auto mt-24">
        <header class="text-center">
            <h2 class="text-2xl font-bold uppercase mb-1">
                Register
            </h2>
            <p class="mb-4">Create an account to post music</p>
        </header>

        <form method="POST" action="/users">
            @csrf
            <div class="mb-6">
                <label for="name" class="inline-block text-lg mb-2">
                    Name
                </label>
                <input type="text" class="border border-gray-200 rounded p-2 w-full" name="name" value="{{ old('name') }}" />
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="email" class="inline-block text-lg mb-2">Email</label>
                <input type="email" class="border border-gray-200 rounded p-2 w-full" name="email" value="{{ old('email') }}" />
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6 relative">
                <label for="password" class="inline-block text-lg mb-2">
                    Password
                </label>
                <div class="relative">
                    <input type="password" class="border border-gray-200 rounded p-2 w-full" name="password" id="password" value="{{ old('password') }}" />
                    <button id="show-password" type="button" class="absolute top-1/2 transform -translate-y-1/2 right-2 text-gray-500 hover:text-gray-700">
                        <i id="eye-icon" class="fa fa-eye"></i>
                    </button>
                </div>
                <p id="password-checklist" class="text-sm mt-1"></p>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>            
            

            <div class="mb-6 relative">
                <label for="password_confirmation" class="inline-block text-lg mb-2">
                    Confirm Password
                </label>
                <div class="relative">
                    <input type="password" class="border border-gray-200 rounded p-2 w-full" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}" />
                    <button id="show-confirm-password" type="button" class="absolute top-1/2 transform -translate-y-1/2 right-2 text-gray-500 hover:text-gray-700">
                        <i id="confirm-eye-icon" class="fa fa-eye"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            

            <div class="mb-6">
                <button type="submit" class="bg-laravel text-white rounded py-2 px-4 hover:bg-black">
                    Sign Up
                </button>
            </div>

            <div class="mt-8">
                <p>
                    Already have an account?
                    <a href="/login" class="text-laravel">Login</a>
                </p>
            </div>
        </form>
    </x-card>
</x-layout>
