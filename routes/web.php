<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

// ===================================
// Home
// ===================================
Route::get('/', function () {
    return redirect()->route('events.index');
});

// ===================================
// Dashboard
// ===================================
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');
//hello world chirakhan
// ===================================
// IMPORTANT — create MUST come before
// {event} otherwise Laravel thinks
// "create" is an event ID!
// ===================================

// Anyone can see all events
Route::get('/events', [EventController::class, 'index'])
    ->name('events.index');

// Logged in users can see create form
// THIS MUST BE BEFORE /events/{event}
Route::middleware('auth')->group(function () {

    Route::get('/events/create', [EventController::class, 'create'])
        ->name('events.create');

    Route::post('/events', [EventController::class, 'store'])
        ->name('events.store');

    Route::get('/events/{event}/edit', [EventController::class, 'edit'])
        ->name('events.edit');

    Route::put('/events/{event}', [EventController::class, 'update'])
        ->name('events.update');

    Route::delete('/events/{event}', [EventController::class, 'destroy'])
        ->name('events.destroy');
});

// Anyone can view a single event
// THIS MUST BE AFTER /events/create
Route::get('/events/{event}', [EventController::class, 'show'])
    ->name('events.show');
