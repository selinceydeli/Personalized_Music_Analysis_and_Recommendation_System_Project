@props(['nonFriend'])
@props(['playlist'])

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
            <form id="addFriendForm_{{ $nonFriend->username }}" action="/addusers/{{$playlist->id}}/{{ $nonFriend['username'] }}" method="POST">
                @csrf
                <input type="hidden" name="user_to_add" value="{{ $nonFriend['username'] }}">
                <button class="px-4 py-2 bg-pink-300 text-white rounded add-friend-button">
                    <i class="fa-solid fa-user-plus"></i> Add
                </button>
            </form>
        </div>
    </div>
    <div class="flex mt-4">
        <img class="w-48 h-48 mr-6 md:block"
        src="/api/users/{{ $nonFriend['username'] }}/getimg" alt="Profile Image" />
        <div>
            <div class="flex items-center">
                <p class="flex items-center">
                    <i class="fa-solid fa-calendar text-lg"></i>
                    <span class="ml-2 text-lg"> Joined at {{ date('d M Y', strtotime($nonFriend['created_at'])) }}
                    </span>
                </p>
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