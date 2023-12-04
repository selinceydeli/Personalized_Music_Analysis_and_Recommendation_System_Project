<?php

use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PerformerController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\DashboardController; // Import DashboardController
use App\Http\Controllers\SpotifyController;

use Illuminate\Support\Facades\Route;

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

Route::post('/upload-via-spotify', [SpotifyController::class, 'importSong'])->name('importSong');

// Single Album
Route::get('/albums/{album}', [AlbumController::class, 'show']);

// Single Performer
Route::get("/performers/{performerId}", [PerformerController::class, 'show']);

Route::post('/users/authenticate', [UserController::class, 'authenticate'])->name('login');

// Dashboard
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'show'])->name('subscription.show');
    Route::get('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');
});

// Recommendations
Route::get('/dashboard/genretaste', [UserController::class, 'showDashboard'])
    ->name('dashboard.genretaste')
    ->middleware('auth');

Route::get('/dashboard/energy', [UserController::class, 'showDashboardEnergy'])
    ->name('dashboard.energy')
    ->middleware('auth');

// Logout
Route::post('/logout', [DashboardController::class, 'logout'])->middleware(['auth'])->name('logout');

Route::get('/search-songs', [SongController::class, 'searchSongs']);

Route::get('/hello', function () {
    return "Hello World";
});

require __DIR__ . '/auth.php';
