<!-- resources/views/subscription/show.blade.php -->

<x-layout>
    <div class="subscription-container">
        <h2 class="text-2xl font-bold mb-4">Subscription Preferences</h2>

        <div class="subscription-options grid grid-cols-2 gap-4">
            <!-- Premium Subscription Box -->
            <x-card class="subscription-option">
                <h3 class="text-lg font-bold mb-2">Premium Subscription</h3>
                <p class="mb-2">Get exclusive features and benefits with our Premium Subscription.</p>
                <ul class="list-disc pl-4">
                    <li>Feature 1</li>
                    <li>Feature 2</li>
                    <!-- Add more features as needed -->
                </ul>
                <p class="text-lg font-bold mt-4">$9.99/month</p>
                <button class="btn btn-primary mt-2">Upgrade</button>
            </x-card>

            <!-- Free Subscription Box -->
            <x-card class="subscription-option">
                <h3 class="text-lg font-bold mb-2">Free Subscription</h3>
                <p class="mb-2">Enjoy basic features with our Free Subscription.</p>
                <ul class="list-disc pl-4">
                    <li>Basic Feature 1</li>
                    <li>Basic Feature 2</li>
                    <!-- Add more basic features as needed -->
                </ul>
                <p class="text-lg font-bold mt-4">Free</p>
                <button class="btn btn-secondary mt-2">Upgrade</button>
            </x-card>
        </div>
    </div>
</x-layout>
