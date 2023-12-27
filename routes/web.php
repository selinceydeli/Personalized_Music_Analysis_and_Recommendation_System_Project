<?php

use App\Models\Song;
use App\Models\PerformerRating;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\MysqlController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerformerController;
use App\Http\Controllers\SongRatingController;
use App\Http\Controllers\AlbumRatingController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PerformerRatingController;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [SongController::class, 'index']);

// Show Register/Create Form
Route::get('/register', [UserController::class, 'create'])->middleware('guest');

// Process Registration Form
Route::post('/users', [UserController::class, 'store'])->name('register');

// Show Login Form
Route::get('/login', [UserController::class, 'login'])->name('login')->middleware('guest');

Route::get('/add', [SongController::class, 'add'])->name('add')->middleware(['auth']);

Route::get('/songs/{id}', [SongController::class, 'show'])->name('show');

Route::post('/upload-via-spotify', [SpotifyController::class, 'importSong'])->name('importSong');

Route::post('/migrateMysql', [MysqlController::class, 'migrateMysql'])->name('migrateMysql');

Route::post('/ratesong', [SongRatingController::class, 'store'])->name('store')->middleware(['auth']);

Route::post('/ratealbum', [AlbumRatingController::class, 'store'])->name('store')->middleware(['auth']);

Route::post('/rateperformer', [PerformerRatingController::class, 'store'])->name('store')->middleware(['auth']);

Route::post('/deletesong/{id}', [SongController::class, 'destroy'])->name('destroy')->middleware(['auth']);

Route::post('/deletealbum/{id}', [AlbumController::class, 'destroy'])->name('destroy')->middleware(['auth']);

Route::post('/deleteperformer/{id}', [PerformerController::class, 'destroy'])->name('destroy')->middleware(['auth']);

Route::get('/addfriends', [UserController::class, 'addfriends'])->name('addfriends')->middleware(['auth']);

Route::get('/myfriends', [UserController::class, 'myfriends'])->name('myfriends')->middleware(['auth']);

Route::get('/requests', [UserController::class, 'requests'])->name('requests')->middleware(['auth']);

Route::get('/blocks', [UserController::class, 'blocks'])->name('blocks')->middleware(['auth']);

Route::post('/add/{user}', [FriendshipController::class, 'sendRequestWeb'])->name('sendRequestWeb')->middleware(['auth']);

Route::post('/unrequest', [FriendshipController::class, 'unrequest'])->name('unrequest')->middleware(['auth']);

Route::post('/accept', [FriendshipController::class, 'acceptRequest'])->name('acceptRequest')->middleware(['auth']);

Route::post('/reject', [FriendshipController::class, 'rejectRequest'])->name('rejectRequest')->middleware(['auth']);

Route::post('/unfriend/{user}', [FriendshipController::class, 'unfriend'])->name('unfriend')->middleware(['auth']);

Route::post('/block', [FriendshipController::class, 'block'])->name('block')->middleware(['auth']);

Route::post('/unblock/{user}', [FriendshipController::class, 'unblock'])->name('unblock')->middleware(['auth']);



// Single Album
Route::get('/albums/{album}', [AlbumController::class, 'show']);

// Single Performer
Route::get("/performers/{performerId}", [PerformerController::class, 'show']);

Route::post('/users/authenticate', [UserController::class, 'authenticate'])->name('login');

// Dashboard
Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    //Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');

    //Subscription
    Route::get('/subscription', [SubscriptionController::class, 'show'])->name('subscription.show');
    Route::get('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');

    Route::post('/pay', [SubscriptionController::class, 'pay'])->name('pay');
    Route::post('/free', [SubscriptionController::class, 'free'])->name('free');


    Route::get('/payment', function () {
        // You can return the Blade view for the payment form here
        $plan = request('plan');
        return view('subscription.payment', ['plan' => $plan]);
    })->name('payment');
});


// Analysis
Route::get('/analysis/favorite_albums', [AlbumRatingController::class, 'topRatedAlbumsByEra'])->middleware(['auth', 'verified'])->name('analysis.favorite_albums');
Route::post('/analysis/favorite_albums', [AlbumRatingController::class, 'topRatedAlbumsByEra'])->middleware(['auth', 'verified'])->name('analysis.favorite_albums.post');

Route::get('/analysis/favorite_songs', [SongRatingController::class, 'favorite10RatingsInGivenMonths'])->middleware(['auth', 'verified'])->name('analysis.favorite_songs');
Route::post('/analysis/favorite_songs', [SongRatingController::class, 'favorite10RatingsInGivenMonths'])->middleware(['auth', 'verified'])->name('analysis.favorite_songs.post');

Route::get('/analysis/average_ratings', [PerformerRatingController::class, 'getAverageRatingsForArtists'])->middleware(['auth', 'verified'])->name('analysis.average_ratings');
Route::post('/analysis/average_ratings', [PerformerRatingController::class, 'getAverageRatingsForArtists'])->middleware(['auth', 'verified'])->name('analysis.average_ratings.post');

Route::get('/analysis/daily_average', [SongRatingController::class, 'getMonthlyAverageRatings'])->middleware(['auth', 'verified'])->name('analysis.daily_average');
Route::get('/search-artists', [PerformerRatingController::class, 'searchArtists']);


// Recommendations
Route::get('/dashboard/genretaste', [UserController::class, 'showDashboard'])
    ->name('dashboard.genretaste')
    ->middleware('auth');

Route::get('/dashboard/energy', [UserController::class, 'showDashboardEnergy'])
    ->name('dashboard.energy')
    ->middleware('auth');

Route::get('/dashboard/positivevalence', [UserController::class, 'showDashboardPositive'])
    ->name('dashboard.positivevalence')
    ->middleware('auth');

Route::get('/dashboard/negativevalence', [UserController::class, 'showDashboardNegative'])
    ->name('dashboard.negativevalence')
    ->middleware('auth');

// Downloading songs
Route::get('/download-all-rated-songs', [SongController::class, 'downloadAllRatedSongs']);
Route::get('/downloads', function () {
    return view('components.downloads');
})->middleware('auth');

// Logout
Route::post('/logout', [DashboardController::class, 'logout'])->middleware(['auth'])->name('logout');

Route::get('/search-songs', [SongController::class, 'searchSongs']);

Route::get('/hello', function () {
    return "Hello World";
});

require __DIR__ . '/auth.php';

// Downloading recommendations
Route::get('/download-recommendations', [UserController::class, 'downloadRecommendations'])->middleware('auth');
Route::get('/download-recommendations-energy', [UserController::class, 'downloadRecommendationsEnergy'])->middleware('auth');
Route::get('/download-positive-recommendations', [UserController::class, 'downloadPositiveRecommendations'])->middleware('auth');
Route::get('/download-negative-recommendations', [UserController::class, 'downloadNegativeRecommendations'])->middleware('auth');

// Importing songs with json file
Route::post('/import-json', [SpotifyController::class, 'importJSON'])->name('import-json');

Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans')->middleware(['auth']);
Route::get('/plans-register', [SubscriptionController::class, 'plans'])->name('plans');
