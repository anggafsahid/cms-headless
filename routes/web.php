<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\TeamController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes (Optional, if you want to provide access to homepage, etc.)
Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

// Admin Routes Dashboard - Protected by auth middleware
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Ensure this is named 'admin.dashboard'
});

// Admin Routes for Pages (already correct)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('pages', PageController::class); // This automatically creates the needed routes for index, show, edit, update, and destroy
});

// Media routes
Route::prefix('admin')->middleware('auth')->group(function () {
    // Display all media
    Route::get('/media', [MediaController::class, 'index'])->name('admin.media.index');
    // Upload new media
    Route::post('/media', [MediaController::class, 'store'])->name('admin.media.store');
});


// Team routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Team Management Routes (CRUD)
    Route::resource('team', TeamController::class);
});


// Public Routes for Pages
Route::get('pages', [PageController::class, 'publicIndex'])->name('pages.index');  // Public index
Route::get('page/{slug}', [PageController::class, 'publicShow'])->name('page.show');

// Public Routes for Media
Route::get('media', [MediaController::class, 'publicIndex'])->name('media.index');

// Public Routes for Team
Route::get('team', [TeamController::class, 'publicIndex'])->name('team.index');
