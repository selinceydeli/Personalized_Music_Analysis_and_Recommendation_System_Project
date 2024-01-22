@props(['nonFriend'])
@props(['pending'])


<x-card class="relative">
    <style>
        .block-user-button {
            margin-left: 134px;
        }

        .add-user-button {
            margin-left: 134px;
        }
    </style>
    <div class="flex justify-between items-center">
        <a href="/user/profile/{{ $nonFriend['username'] }}" class="text-2xl">
            <i class="fa-solid fa-user"></i>
            {{ $nonFriend['username'] }}
        </a>
        <div>
            @if ($pending && array_key_exists($nonFriend['username'], $pending))
                <form id="unrequestForm_{{ $nonFriend->username }}" action="/unrequest" method="POST">
                    @csrf
                    <input type="hidden" name="user_to_unrequest" value="{{ $nonFriend['username'] }}">
                    <button onclick="submitForm(this)"
                        class="px-4 py-2 bg-red-500 text-white rounded add-friend-button">
                        <i class="fa-solid fa-check"></i> Request sent
                    </button>
                </form>
            @else
                <form id="addFriendForm_{{ $nonFriend->username }}" action="/add/{{ $nonFriend['username'] }}"
                    method="POST">
                    @csrf
                    <input type="hidden" name="user_to_add" value="{{ $nonFriend['username'] }}">
                    <button onclick="submitForm(this)"
                        class="px-4 py-2 bg-red-500 text-white rounded add-friend-button">
                        <i class="fa-solid fa-user-plus"></i> Add Friend
                    </button>
                </form>
            @endif
        </div>
    </div>
    <div class="flex mt-4">
        <img class="w-48 h-48 mr-6 md:block"
            src="{{ $nonFriend['image'] ? $nonFriend['image'] : asset('/images/default.jpg') }}"
            alt="Profile Image" />
        <div>
            <div class="flex items-center">
                <p class="flex items-center">
                    <i class="fa-solid fa-calendar text-lg"></i>
                    <span class="ml-2 text-lg"> Joined at {{ date('d M Y', strtotime($nonFriend['created_at'])) }}
                    </span>
                </p>
                <form id="blockUserForm_{{ $nonFriend->username }}" action="/block" method="POST">
                    @csrf
                    <input type="hidden" name="user_to_block" value="{{ $nonFriend['username'] }}">
                    <button onclick="submitForm(this)"
                        class="px-5 py-2 bg-red-500 text-white rounded block-user-button">
                        <i class="fa-solid fa-shield"></i> Block User
                    </button>
                </form>
            </div>
            <p class="mt-12">
                <i class="fa-solid fa-users text-lg"></i>
                <span class="ml-2 text-lg"> Friends: {{ $nonFriend['friends_of_mine_count'] }}</span>
            </p>
            <p class="mt-12">
                <i class="fa-solid fa-tag text-lg"></i>
                <span class="ml-2 text-lg"> Plan: {{ ucfirst($nonFriend['subscription']) }}</span>
            </p>
            <!-- Add appropriate icons and style them according to your icon library -->
        </div>
    </div>
</x-card>

<script>
    $(document).ready(function() {
        $(document).on('click', '.block-user-button', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formId = $(this).closest('form').attr('id'); // Get the ID of the clicked form
            var usernameToBlock = $('#' + formId + ' input[name="user_to_block"]').val();

            // You can perform actions here related to blocking the user
            // For example, sending an AJAX request or updating UI elements

            // For demonstration purposes, logging the action is shown

            // You can also submit the form associated with the clicked button
            $('#' + formId).submit();
        });
        $(document).on('click', '.add-friend-button', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formId = $(this).closest('form').attr('id'); // Get the ID of the clicked form
            var usernameToAdd = $('#' + formId + ' input[name="user_to_add"]').val();

            // You can perform actions here related to adding the friend
            // For example, sending an AJAX request or updating UI elements

            // For demonstration purposes, logging the action is shown

            // You can also submit the form associated with the clicked button
            $('#' + formId).submit();
        });
    });
</script>
