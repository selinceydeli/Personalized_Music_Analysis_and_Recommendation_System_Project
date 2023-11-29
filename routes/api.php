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

Route::get('/search/{searchTerm}', [SearchController::class, 'search_all']);

// User recommendation routes
Route::get('/users/{username}/fav-genre-recommendations', [UserController::class, 'favGenreRecomendationFromDifferentPerformers']);
Route::get('/users/{username}/energy-danceability-recommendations', [UserController::class, 'RecomendationByEnergyAndDanceability']);

// Analysis routes
Route::get('/songrating/user/{username}/top-10-in/{months}/months', [SongRatingController::class, 'favorite10RatingsInGivenMonths']);
Route::get('/songrating/user/{username}/monthly-averages', [SongRatingController::class, 'getMonthlyAverageRatings']);
Route::get('/albumrating/top-rated/{username}/{era}', [AlbumRatingController::class, 'topRatedAlbumsByEra']);
Route::post('/performerrating/average-performer-ratings', [PerformerRatingController::class, 'getAverageRatingsForArtists']);

// Handling song imports
Route::post('/spotify/import', [SpotifyController::class, 'importSong']);

