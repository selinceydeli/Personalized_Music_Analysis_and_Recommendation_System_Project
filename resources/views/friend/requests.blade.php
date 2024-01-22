<x-layout>
    @include('partials._searchrequest')
    <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
        <!-- Section for songs list -->
        @if (count($requests) == 0)
            <p>
                You don't have any incoming friend requests
            </p>
        @else
            @foreach ($requests as $request)
                <x-card class="relative">
                    <style>
                        .block-user-button {
                            margin-left: 134px;
                        }
                    </style>
                    <div class="flex justify-between items-center">
                        <a href="/user/profile/{{ $request['username'] }}" class="text-2xl">
                            <i class="fa-solid fa-user"></i>
                            {{ $request['username'] }}
                        </a>
                        
                        <div style="display: flex;">
                            <form id="acceptForm_{{ $request->username }}" action="/accept" method="POST" style="margin-right: 30px;">
                                @csrf
                                <input type="hidden" name="user_to_accept" value="{{ $request['username'] }}">
                                <button onclick="submitForm(this)" class="px-4 py-2 bg-laravel text-white rounded accept-friend-button">
                                    <i class="fa-solid fa-check"></i> Accept
                                </button>
                            </form>
                            <form id="rejectUserForm_{{ $request->username }}" action="/reject" method="POST">
                                @csrf
                                <input type="hidden" name="user_to_reject" value="{{ $request['username'] }}">
                                <button onclick="submitForm()" class="px-4 py-2 bg-laravel text-white rounded reject-user-button">
                                    <i class="fa-solid fa-times"></i> Reject
                                </button>
                            </form>
                        </div>                        
                    </div>
                    <div class="flex mt-4">
                        <img class="w-48 h-48 mr-6 md:block"
                            src="/api/users/{{ $request['username'] }}/getimg"
                            alt="Profile Image" />
                        <div>
                            <div class="flex items-center">
                                <p class="flex items-center">
                                    <i class="fa-solid fa-calendar text-lg"></i>
                                    <span class="ml-2 text-lg"> Joined at
                                        {{ date('d M Y', strtotime($request['created_at'])) }}
                                    </span>
                                </p>
                            </div>
                            <p class="mt-12">
                                <i class="fa-solid fa-users text-lg"></i>
                                <span class="ml-2 text-lg"> Friends: {{ $request['friends_of_mine_count'] }}</span>
                            </p>
                            <p class="mt-12">
                                <i class="fa-solid fa-tag text-lg"></i>
                                <span class="ml-2 text-lg"> Plan: {{ ucfirst($request['subscription']) }}</span>
                            </p>
                            <!-- Add appropriate icons and style them according to your icon library -->
                        </div>
                    </div>
                </x-card>
            @endforeach
        @endif
    </div>
    <div class="mt-6 p-4">
        {{ $requests->links() }}
    </div>
</x-layout>


<script>
    $(document).ready(function() {
        $(document).on('click', '.accept-friend-button', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formId = $(this).closest('form').attr('id'); // Get the ID of the clicked form
            var usernameToAccept = $('#' + formId + ' input[name="user_to_accept"]').val();

            // You can perform actions here related to accepting the friend request
            // For example, sending an AJAX request or updating UI elements

            // For demonstration purposes, logging the action is shown

            // You can also submit the form associated with the clicked button
            $('#' + formId).submit();
        });

        $(document).on('click', '.reject-user-button', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formId = $(this).closest('form').attr('id'); // Get the ID of the clicked form
            var usernameToReject = $('#' + formId + ' input[name="user_to_reject"]').val();

            // You can perform actions here related to rejecting the friend request
            // For example, sending an AJAX request or updating UI elements

            // For demonstration purposes, logging the action is shown

            // You can also submit the form associated with the clicked button
            $('#' + formId).submit();
        });
    });
</script>
