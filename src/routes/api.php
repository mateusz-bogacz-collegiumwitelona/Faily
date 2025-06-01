<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventAttendeeController;
use App\Http\Controllers\Api\PhotoController;
use App\Http\Controllers\Api\RideController;
use Illuminate\Support\Facades\Http;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'me']);
        Route::put('/', [UserController::class, 'update']);
        Route::post('/photo', [UserController::class, 'updatePhoto']);
        Route::put('/password', [UserController::class, 'updatePassword']);
        Route::put('/settings', [UserController::class, 'updateSettings']);
    });

    Route::apiResource('events', EventController::class)->names([
        'index' => 'api.events.index',
        'store' => 'api.events.store',
        'show' => 'api.events.show',
        'update' => 'api.events.update',
        'destroy' => 'api.events.destroy',
    ]);

    Route::get('/events/popular', [EventController::class, 'popular']);
    Route::get('/events/upcoming', [EventController::class, 'upcoming']);

    Route::get('/events/{event}/attendees', [EventAttendeeController::class, 'index']);
    Route::post('/events/{event}/attendees', [EventAttendeeController::class, 'store']);
    Route::patch('/events/{event}/attendees/{attendee}', [EventAttendeeController::class, 'update']);
    Route::delete('/events/{event}/attendees/{attendee}', [EventAttendeeController::class, 'destroy']);
    Route::get('/my-attending', [EventAttendeeController::class, 'myEvents']);
    Route::get('/my-attendees', [EventAttendeeController::class, 'myAttendees']);

    Route::get('/events/{event}/photos', [PhotoController::class, 'index']);
    Route::post('/photos', [PhotoController::class, 'store']);
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);

    Route::apiResource('rides', RideController::class)->names([
        'index' => 'api.rides.index',
        'store' => 'api.rides.store',
        'show' => 'api.rides.show',
        'update' => 'api.rides.update',
        'destroy' => 'api.rides.destroy',
    ]);

    Route::apiResource('ride-requests', RideRequestController::class)->except(['show', 'edit'])->names([
        'index' => 'api.ride-requests.index',
        'store' => 'api.ride-requests.store',
        'update' => 'api.ride-requests.update',
        'destroy' => 'api.ride-requests.destroy',
    ]);

    Route::get('/my-ride-requests', [RideRequestController::class, 'myRequests']);

    Route::get('/geocode/search', 'GeocodingController@search');
    Route::get('/geocode/reverse', 'GeocodingController@reverse');
});

Route::get('/nominatim/search', function (\Illuminate\Http\Request $request) {
    $q = $request->query('q');
    $limit = $request->query('limit', 5);

    $response = Http::withHeaders([
        'User-Agent' => 'YourAppName/1.0 (you@example.com)'
    ])->get('https://nominatim.openstreetmap.org/search', [
        'format' => 'json',
        'q' => $q,
        'limit' => $limit,
    ]);

    \Log::info("Response body: " . $response->body());
    \Log::info("Response status: " . $response->status());

    return $response->json();
});

Route::get('/nominatim/reverse', function (\Illuminate\Http\Request $request) {
    $lat = $request->query('lat');
    $lon = $request->query('lon');

    $response = Http::withHeaders([
        'User-Agent' => 'YourAppName/1.0 (you@example.com)'
    ])->get('https://nominatim.openstreetmap.org/reverse', [
        'format' => 'json',
        'lat' => $lat,
        'lon' => $lon,
    ]);

    return $response->json();
});
