<!-- resources/views/subscription/edit.blade.php -->

<x-layout>
    <div class="subscription-container">
        <h2 class="text-2xl font-bold mb-4">Update Subscription Preferences</h2>

        <form method="POST" action="{{ route('subscription.update') }}">
            @csrf
            @method('put')

            <!-- Subscription preferences update form -->
            <!-- ... -->

            <button type="submit" class="bg-gold text-black rounded py-2 px-4 hover:bg-yellow-600">
                Update Preferences
            </button>
        </form>
    </div>
</x-layout>
