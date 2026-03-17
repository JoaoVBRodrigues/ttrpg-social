<?php

use App\Http\Controllers\Web\CampaignController;
use App\Http\Controllers\Web\CampaignMembershipController;
use App\Http\Controllers\Web\CampaignSessionController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SessionAttendanceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('campaigns', CampaignController::class)
    ->only(['index', 'show'])
    ->scoped(['campaign' => 'slug']);

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

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::resource('campaigns', CampaignController::class)
        ->only(['create', 'store', 'edit', 'update'])
        ->scoped(['campaign' => 'slug']);

    Route::post('campaigns/{campaign:slug}/members/request', [CampaignMembershipController::class, 'requestJoin'])
        ->name('campaigns.members.request');
    Route::post('campaigns/{campaign:slug}/members/invite', [CampaignMembershipController::class, 'invite'])
        ->name('campaigns.members.invite');
    Route::patch('campaign-members/{membership}/review', [CampaignMembershipController::class, 'review'])
        ->name('campaign-members.review');
    Route::delete('campaign-members/{membership}', [CampaignMembershipController::class, 'remove'])
        ->name('campaign-members.remove');
    Route::post('campaigns/{campaign:slug}/leave', [CampaignMembershipController::class, 'leave'])
        ->name('campaigns.leave');
    Route::post('campaigns/{campaign:slug}/sessions', [CampaignSessionController::class, 'store'])
        ->name('campaign-sessions.store');
    Route::put('campaign-sessions/{campaignSession}', [CampaignSessionController::class, 'update'])
        ->name('campaign-sessions.update');
    Route::put('campaign-sessions/{campaignSession}/attendance', [SessionAttendanceController::class, 'update'])
        ->name('campaign-sessions.attendance.update');
});

require __DIR__.'/auth.php';
