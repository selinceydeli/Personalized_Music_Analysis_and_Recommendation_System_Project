@props(['song'])

    <!-- Item 1 -->
    <x-card>
        <div class="flex">
            <div>
                <h3 class="text-2xl">
                    <a href="/songs/{{$song->id}}">{{$song->name}}</a>
                </h3>
            </div>
        </div>
    </x-card>
