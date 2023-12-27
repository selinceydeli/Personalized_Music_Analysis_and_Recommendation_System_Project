<x-layout>
    @include('partials._searchuser')
    <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
        @if (count($nonFriends) == 0)
            <p>
                Everyone is your friend
            </p>
        @else
            @foreach ($nonFriends as $nonFriend)
                <div class="listing-card" data-title="{{ $nonFriend->username }}">
                    <x-nonfriend-card :nonFriend="$nonFriend" :pending="$pending"/>
                </div>
            @endforeach
        @endif
    </div>
    <div class="mt-6 p-4">
        {{ $nonFriends->links() }}
    </div>
</x-layout>
