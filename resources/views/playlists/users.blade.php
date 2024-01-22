<x-layout>
    @include('partials._searchusers')
    <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
        @if (count($users) == 0)
            <p>
                No more remaining friends to add
            </p>
        @else
            @foreach ($users as $nonFriend)
                <div class="listing-card" data-title="{{ $nonFriend->username }}">
                    <x-users-card :nonFriend="$nonFriend" :playlist="$playlist"/>
                </div>
            @endforeach
        @endif
    </div>
    <div class="mt-6 p-4">
        {{ $users->links() }}
    </div>
</x-layout>