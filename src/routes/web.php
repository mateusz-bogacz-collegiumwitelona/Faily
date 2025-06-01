<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\EventAttendeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MainMapController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\RideRequestController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BannedController;
use App\Http\Controllers\UserAttendancesController;


Route::get('language/{locale}', [LanguageController::class, 'changeLanguage'])->name('language.change')->middleware(['locale']);

Route::get('/', function () {return view('welcome');})->name('welcome')->middleware('locale');

Route::get('/about', function () {return view('about');})->middleware('locale');

Route::middleware(['auth', 'verified', 'locale'])->group(function ()
{
    Route::get('/profile/dashboard', function () {return view('profile.dashboard');})->name('profile.dashboard');
    Route::match(['put', 'patch'],'/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show.blade.php');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/photo', [ProfileController::class, 'editPhoto'])->name('profile.edit-photo');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update-photo');

    Route::get('/user/{user}', [ProfileController::class, 'showUserProfile'])->name('user.profile');

    Route::get('/events/feed', [EventController::class, 'feed'])->name('events.feed');

    Route::resource('events', EventController::class)->names('events');
    Route::resource('rides', RideController::class)->names('rides');
    Route::resource('ride-requests', RideRequestController::class)->names('ride-requests');
    Route::get('/ride_requests', [RideRequestController::class, 'index'])->name('ride_requests.index');
    Route::get('/ride_requests/create', [RideRequestController::class, 'create'])->name('ride_requests.create');

    Route::get('/events/{event}/attendees', [EventAttendeeController::class, 'index'])->name('events.attendees.index');
    Route::get('/events/{event}/attend', [EventAttendeeController::class, 'create'])->name('events.attendees.create');
    Route::post('/events/{event}/attend', [EventAttendeeController::class, 'store'])->name('events.attendees.store');
    Route::patch('/events/{event}/attendees/{attendee}', [EventAttendeeController::class, 'update'])->name('events.attendees.update');
    Route::delete('/events/{event}/attendees/{attendee}', [EventAttendeeController::class, 'destroy'])->name('events.attendees.destroy');

    Route::get('/my_events', [EventController::class, 'myEvents'])->name('my_events');
    Route::get('/all_events', [EventController::class, 'allEvents'])->name('all_events');
    Route::get('/event_list', [EventController::class, 'Events_list'])->name('event_list');
    Route::get('/event_list.index', [EventController::class, 'index'])->name('event_list_index');


    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

    Route::get('/add_event', function () {return view('events.create');});
    Route::get('/account', function () {return view('account');});
    Route::get('/map', [MainMapController::class, 'showMap']);
    Route::get('/help', function () {return view('help');});
    Route::get('/my-attendances', [UserAttendancesController::class, 'index'])->name('user.attendances');


    Route::get('/my-applications', [EventAttendeeController::class, 'myApplications'])->name('my-applications');


});

Route::middleware(['auth', 'verified', 'locale', 'admin'])->prefix('admin')->name('admin.')->group(function ()
{
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{id}/ban', [AdminController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{id}/unban', [AdminController::class, 'unbanUser'])->name('users.unban');
    Route::post('/users/{id}/make-admin', [AdminController::class, 'makeAdmin'])->name('users.make-admin');
    Route::post('/users/{id}/remove-admin', [AdminController::class, 'removeAdmin'])->name('users.remove-admin');

    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::post('/reports/{id}/approve', [AdminController::class, 'approveReport'])->name('reports.approve');
    Route::post('/reports/{id}/reject', [AdminController::class, 'rejectReport'])->name('reports.reject');
});

Route::middleware(['auth', 'locale', 'banned'])->get('/banned', [BannedController::class, 'index'])->name('banned');

Route::middleware(['auth', 'locale'])->post('/users/{id}/report', [ReportController::class, 'reportUser'])->name('users.report');

require __DIR__.'/auth.php';

