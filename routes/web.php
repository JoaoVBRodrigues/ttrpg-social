<?php

use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('u/{user:username}', [ProfileController::class, 'showPublic'])
    ->name('profile.public');

Route::get('profile', [ProfileController::class, 'edit'])
    ->middleware(['auth'])
    ->name('profile');

Route::patch('profile', [ProfileController::class, 'update'])
    ->middleware(['auth'])
    ->name('profile.update');

Route::put('profile/preferences', [ProfileController::class, 'updatePreferences'])
    ->middleware(['auth'])
    ->name('profile.preferences.update');

require __DIR__.'/auth.php';
