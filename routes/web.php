<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskMessageController;
use App\Http\Controllers\ProjectMemberController;


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'storeConversation'])->name('messages.storeConversation');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
});
Route::post('/projects/{project}/members/create-user', [ProjectMemberController::class, 'createUser'])
    ->name('projects.members.create-user');

Route::middleware(['auth', 'verified'])->group(function () {


    Route::patch('/settings/notifications', [ProfileController::class, 'updateNotifications'])
        ->name('profile.notifications');

    Route::get('/team', [TeamController::class, 'index'])
        ->name('team.index');

    Route::post('/team/{user}/add-to-project', [TeamController::class, 'addToProject'])
        ->name('teams.addToProject');


    Route::get('/calendar', [CalendarController::class, 'index'])
        ->name('calendar.index');


    Route::post('/calendar/events', [CalendarController::class, 'store'])
        ->name('calendar.events.store');


    Route::put('/calendar/events/{event}', [CalendarController::class, 'update'])
        ->name('calendar.events.update');


    Route::delete('/calendar/events/{event}', [CalendarController::class, 'destroy'])
        ->name('calendar.events.destroy');


    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');


    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show');


    Route::resource('projects', ProjectController::class);


    Route::resource('projects.tasks', TaskController::class)
        ->except(['show'])
        ->scoped();

    Route::patch(
        '/tasks/{task}/status',
        [TaskController::class, 'updateStatus']
    )->name('tasks.update-status');


    Route::post(
        '/projects/{project}/invitations',
        [ProjectInvitationController::class, 'store']
    )->name('projects.invitations.store');

    Route::patch(
        '/invitations/{invitation}/accept',
        [ProjectInvitationController::class, 'accept']
    )->name('invitations.accept');

    Route::patch(
        '/invitations/{invitation}/reject',
        [ProjectInvitationController::class, 'reject']
    )->name('invitations.reject');

    Route::get(
        '/invitations',
        [ProjectInvitationController::class, 'index']
    )->name('invitations.index');

    Route::patch(
        '/projects/{project}/members/{user}/role',
        [ProjectInvitationController::class, 'changeMemberRole']
    )->name('projects.members.change-role');

    Route::delete(
        '/projects/{project}/members/{user}',
        [ProjectInvitationController::class, 'destroy']
    )->name('projects.members.destroy');



    Route::get(
        '/notifications',
        [NotificationController::class, 'index']
    )->name('notifications.index');

    Route::patch(
        '/notifications/{notification}/read',
        [NotificationController::class, 'markAsRead']
    )->name('notifications.markAsRead');

    Route::patch(
        '/notifications/read-all',
        [NotificationController::class, 'readAll']
    )->name('notifications.readAll');

    Route::get(
        '/notifications/{notification}/open',
        [NotificationController::class, 'open']
    )->name('notifications.open');
});


Route::get('/analytics', [AnalyticsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('analytics.index');

Route::post('/tasks/{task}/messages', [TaskMessageController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('tasks.messages.store');


Route::get('/', [HomeController::class, 'home'])->name('home');


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-calendar', function () {
    return view('test-calendar');
});


require __DIR__.'/auth.php';
