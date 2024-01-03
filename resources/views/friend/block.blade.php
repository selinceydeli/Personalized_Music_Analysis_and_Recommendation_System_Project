<x-layout>
    @include('partials._searchblock')
    <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
        <!-- Section for songs list -->
        @if (count($blocks) == 0)
            <p>
                You don't have any blocked users
            </p>
        @else
            @foreach ($blocks as $block)
                <x-card class="relative">
                    <div class="flex justify-between items-center">
                        <a href="/user/profile/{{ $block['username'] }}" class="text-2xl">
                            <i class="fa-solid fa-user"></i>
                            {{ $block['username'] }}
                        </a>
                        
                        <div style="display: flex;">
                            <form id="unblockForm_{{ $block->username }}" action="/unblock/{{ $block['username'] }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_to_unblock" value="{{ $block['username'] }}">
                                <button onclick="submitForm(this)" class="px-4 py-2 bg-red-500 text-white rounded unblock-button">
                                    <i class="fa-solid fa-undo"></i> Unblock
                                </button>
                            </form>
                        </div>                        
                    </div>
                    <div class="flex mt-4">
                        <img class="w-48 h-48 mr-6 md:block"
                            src="{{ $block['image'] ? $block['image'] : asset('/images/default.jpg') }}"
                            alt="Profile Image" />
                        <div>
                            <div class="flex items-center">
                                <p class="flex items-center">
                                    <i class="fa-solid fa-calendar text-lg"></i>
                                    <span class="ml-2 text-lg"> Joined at
                                        {{ date('d M Y', strtotime($block['created_at'])) }}
                                    </span>
                                </p>
                            </div>
                            <p class="mt-12">
                                <i class="fa-solid fa-users text-lg"></i>
                                <span class="ml-2 text-lg"> Friends: {{ $block['friends_of_mine_count'] }}</span>
                            </p>
                            <p class="mt-12">
                                <i class="fa-solid fa-tag text-lg"></i>
                                <span class="ml-2 text-lg"> Plan: {{ ucfirst($block['subscription']) }}</span>
                            </p>
                            <!-- Add appropriate icons and style them according to your icon library -->
                        </div>
                    </div>
                </x-card>
            @endforeach
        @endif
    </div>
    <div class="mt-6 p-4">
        {{ $blocks->links() }}
    </div>
</x-layout>


<script>
    $(document).ready(function() {
        $(document).on('click', '.unblock-button', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formId = $(this).closest('form').attr('id'); // Get the ID of the clicked form
            var usernameToAccept = $('#' + formId + ' input[name="user_to_unblock"]').val();

            // You can perform actions here related to accepting the friend request
            // For example, sending an AJAX request or updating UI elements

            // For demonstration purposes, logging the action is shown

            // You can also submit the form associated with the clicked button
            $('#' + formId).submit();
        });
    });
</script>
