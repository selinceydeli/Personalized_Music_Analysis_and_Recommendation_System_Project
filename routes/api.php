<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\PerformerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SongRatingController;
use App\Http\Controllers\AlbumRatingController;
use App\Http\Controllers\PerformerRatingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\BlockController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/songs', [SongController::class, 'index']);
Route::get('/songs/{id}', [SongController::class, 'search_id']);
Route::get('/songs/name/{searchTerm}', [SongController::class, 'search_name']);
Route::put('/songs/{id}', [SongController::class, 'update']);
Route::post('/songs', [SongController::class, 'store']);
Route::delete('/songs/{id}', [SongController::class, 'destroy']);
Route::get('/songs/genre/{genre}', [SongController::class, 'getSongsByGenre']);

Route::get('/albums', [AlbumController::class, 'index']);
Route::get('/albums/{id}', [AlbumController::class, 'search_id']);
Route::get('/albums/name/{searchTerm}', [AlbumController::class, 'search_name']);
Route::put('/albums/{id}', [AlbumController::class, 'update']);
Route::post('/albums', [AlbumController::class, 'store']);
Route::delete('/albums/{id}', [AlbumController::class, 'destroy']);

Route::get('/performers', [PerformerController::class, 'index']);
Route::get('/performers/{id}', [PerformerController::class, 'search_id']);
Route::get('/performers/name/{searchTerm}', [PerformerController::class, 'search_name']);
Route::put('/performers/{id}', [PerformerController::class, 'update']);
Route::post('/performers', [PerformerController::class, 'store']);
Route::delete('/performers/{id}', [PerformerController::class, 'destroy']);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{username}', [UserController::class, 'search_username']);
Route::put('/users/{username}', [UserController::class, 'update']);
Route::post('/users', [UserController::class, 'store']);
Route::delete('/users/{username}', [UserController::class, 'destroy']);
Route::put('/users/{username}', [UserController::class, 'update']);
Route::get('/users/{username}/getimg', [UserController::class, 'getImg']);
Route::post('/users/{username}/uploadImg', [UserController::class, 'uploadImg']);

Route::get('/songrating', [SongRatingController::class, 'index']);
Route::get('/songrating/song/{id}', [SongRatingController::class, 'search_id_song']);
Route::get('/songrating/user/{username}', [SongRatingController::class, 'search_id_user']);
Route::put('/songrating/{id}', [SongRatingController::class, 'update']);
Route::post('/songrating', [SongRatingController::class, 'store']);
Route::delete('/songrating/{id}', [SongRatingController::class, 'destroy']);

Route::get('/albumrating', [AlbumRatingController::class, 'index']);
Route::get('/albumrating/album/{id}', [AlbumRatingController::class, 'search_id_album']);
Route::get('/albumrating/user/{username}', [AlbumRatingController::class, 'search_id_user']);
Route::put('/albumrating/{id}', [AlbumRatingController::class, 'update']);
Route::post('/albumrating', [AlbumRatingController::class, 'store']);
Route::delete('/albumrating/{id}', [AlbumRatingController::class, 'destroy']);

Route::get('/performerrating', [PerformerRatingController::class, 'index']);
Route::get('/performerrating/performer/{id}', [PerformerRatingController::class, 'search_id_performer']);
Route::get('/performerrating/user/{username}', [PerformerRatingController::class, 'search_id_user']);
Route::put('/performerrating/{id}', [PerformerRatingController::class, 'update']);
Route::post('/performerrating', [PerformerRatingController::class, 'store']);
Route::delete('/performerrating/{id}', [PerformerRatingController::class, 'destroy']);

// Search Routes
Route::get('/search/{searchTerm}', [SearchController::class, 'search_all']);
Route::get('/search/song/{searchTerm}', [SearchController::class, 'search_song']);
Route::get('/search/album/{searchTerm}', [SearchController::class, 'search_album']);
Route::get('/search/performer/{searchTerm}', [SearchController::class, 'search_performer']);

// User recommendation routes
Route::get('/users/{username}/fav-genre-recommendations', [UserController::class, 'favGenreRecomendationFromDifferentPerformers']);
Route::get('/users/{username}/energy-danceability-recommendations', [UserController::class, 'RecomendationByEnergyAndDanceability']);
Route::get('/users/{username}/moody-recommendations', [UserController::class, 'favNegativeRecomendation']);
Route::get('/users/{username}/positive-recommendations', [UserController::class, 'favPositiveRecomendation']);

// Analysis routes
Route::get('/songrating/user/{username}/top-10-in/{months}/months', [SongRatingController::class, 'favorite10RatingsInGivenMonths']);
Route::get('/songrating/user/{username}/monthly-averages', [SongRatingController::class, 'getMonthlyAverageRatings']);
Route::get('/albumrating/top-rated/{username}/{era}', [AlbumRatingController::class, 'topRatedAlbumsByEra']);
Route::post('/performerrating/average-performer-ratings', [PerformerRatingController::class, 'getAverageRatingsForArtists']);

// Friendship Routes
Route::post('/friend-request/{user}', [FriendshipController::class, 'sendRequestWeb']);
Route::post('/friend-request-mobile/{requester}/{userequested}', [FriendshipController::class, 'sendRequestMobile']);
Route::post('/accept-request', [FriendshipController::class, 'acceptRequest']); // Send with a JSON that has 'requester', 'user_requested'
Route::post('/see-request', [FriendshipController::class, 'seeRequests']);
Route::post('/see-request-mobile/{username}', [FriendshipController::class, 'seeRequestsMobile']);
Route::post('/unfriend/{user}', [FriendshipController::class, 'unfriend']);
Route::post('/unfriend-mobile/{currentuser}/{unfriend}', [FriendshipController::class, 'unfriendMobile']);

// Blocking Routes
Route::post('/block-user/{blockedUsername}', [BlockController::class, 'blockUser']);
Route::post('/unblock-user/{blockedUsername}', [BlockController::class, 'unblockUser']);
Route::post('/block-user-mobile', [BlockController::class, 'blockUserMobile']); // Send with a JSON that has 'blocker_username', 'blocked_username'
Route::post('/unblock-user-mobile', [BlockController::class, 'unblockUserMobile']); // Send with a JSON that has 'blocker_username', 'blocked_username'

// Getting friends and blocked users
Route::get('/user/{username}/friends', [UserController::class, 'getFriends']);
Route::get('/user/{username}/blocked', [UserController::class, 'getBlockedUsers']);

// Handling song imports
Route::post('/spotify/import', [SpotifyController::class, 'importSong']);

// Mobile Login
Route::post('/user/mobilelogin', [UserController::class, 'mobileauthenticate']);

// Playlist
Route::post('/playlists/create-with-user', [PlaylistController::class, 'storeWithUser'])->name('playlists.storeWithUser');
Route::get('/playlists/{username}', [PlaylistController::class, 'getUserPlaylists'])->name('user.playlists');
Route::post('/playlist/{playlistId}/songs', [PlaylistController::class, 'addSongsToPlaylist'])->name('playlist.addSongs');
Route::post('/playlist/{playlistId}/users', [PlaylistController::class, 'addUsersToPlaylist'])->name('playlist.addUsers');
Route::get('/playlist/{playlistId}', [PlaylistController::class, 'getPlaylistSongs'])->name('playlist.getPlaylistSongs');
Route::delete('/playlist/{playlistId}/song/{songId}', [PlaylistController::class, 'removeSongFromPlaylist'])->name('playlist.removeSong');
Route::delete('/playlist/{playlistId}', [PlaylistController::class, 'destroy'])->name('playlist.destroy');

// Stories
Route::get('/users/{username}/top5-songs', [UserController::class, 'top5Songs']);
Route::get('/users/{username}/song-of-year', [UserController::class, 'songOfYear']);
Route::get('/users/{username}/top5-albums', [UserController::class, 'top5Albums']);
Route::get('/users/{username}/top5-genres', [UserController::class, 'favGenres']);

