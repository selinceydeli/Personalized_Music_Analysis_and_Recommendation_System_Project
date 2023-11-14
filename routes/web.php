<?php

use App\Http\Controllers\AlbumController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController; // Import DashboardController

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

//Single Album
Route::get('/albums/{album}', [AlbumController::class, 'show']);

Route::post('/users/authenticate', [UserController::class, 'authenticate'])->name('login');

// Add the route for the Dashboard
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Add the route for Logout
Route::post('/logout', [DashboardController::class, 'logout'])->middleware(['auth'])->name('logout');

Route::get('/search-songs', [SongController::class,'searchSongs']);


Route::get('/hello', function(){
    return "Hello World";
});

require __DIR__.'/auth.php';
