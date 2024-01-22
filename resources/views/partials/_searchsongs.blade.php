<form action="/addsong/{{$playlist->id}}">
    <div class="relative border-2 border-gray-100 m-4 rounded-lg">
        <div class="absolute top-4 left-3">
            <i class="fa fa-search text-gray-400 z-20 hover:text-gray-500"></i>
        </div>
        <input type="text" name="searchaddsong" class="h-14 w-full pl-10 pr-20 rounded-lg z-0 focus:shadow focus:outline-none" placeholder="Search Music..." />
        <div class="absolute top-2 right-2">
            <button type="submit" class="h-10 w-20 text-white rounded-lg custom-button-bg hover:custom-button-hover-bg">
                Search
            </button>
        </div>
    </div>
</form>

<style>
    .custom-button-bg {
        background-color: #ff2c54; /* Your desired color */
    }

    .custom-button-hover-bg:hover {
        background-color: #ff2c54; /* Change this if you want a different hover color */
    }
</style>
