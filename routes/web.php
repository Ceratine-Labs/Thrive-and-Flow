<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/* ------------------------------------------------------------ public site */

Route::get('/', HomeController::class)->name('home');
Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

/* ------------------------------------------------------------------- auth */

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/* ---------------------------------------------------------- member studio */

// The player is public so free-preview classes work for guests;
// it redirects to login / pricing / the program page as needed.
Route::get('/studio/watch/{lesson}', PlayerController::class)->name('studio.watch');

Route::middleware('auth')->prefix('studio')->name('studio.')->group(function () {
    Route::get('/', StudioController::class)->name('dashboard');
    Route::post('/watch/{lesson}/complete', [ProgressController::class, 'toggleComplete'])->name('complete');
    Route::post('/watch/{lesson}/favorite', [FavoriteController::class, 'toggle'])->name('favorite');
    Route::post('/subscribe/{plan:key}', [SubscriptionController::class, 'start'])->name('subscribe');
    Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
});

/* -------------------------------------------------------- studio manager */

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::get('/programs', [Admin\ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/create', [Admin\ProgramController::class, 'create'])->name('programs.create');
    Route::post('/programs', [Admin\ProgramController::class, 'store'])->name('programs.store');
    Route::get('/programs/{program}/builder', [Admin\ProgramController::class, 'builder'])->name('programs.builder');
    Route::put('/programs/{program}', [Admin\ProgramController::class, 'update'])->name('programs.update');
    Route::post('/programs/{program}/publish', [Admin\ProgramController::class, 'togglePublish'])->name('programs.publish');

    Route::post('/programs/{program}/weeks', [Admin\WeekController::class, 'store'])->name('weeks.store');
    Route::put('/weeks/{week}', [Admin\WeekController::class, 'update'])->name('weeks.update');
    Route::delete('/weeks/{week}', [Admin\WeekController::class, 'destroy'])->name('weeks.destroy');

    Route::post('/programs/{program}/lessons', [Admin\LessonController::class, 'store'])->name('lessons.store');
    Route::put('/lessons/{lesson}', [Admin\LessonController::class, 'update'])->name('lessons.update');
    Route::post('/lessons/{lesson}/publish', [Admin\LessonController::class, 'togglePublish'])->name('lessons.publish');
    Route::post('/lessons/{lesson}/preview', [Admin\LessonController::class, 'togglePreview'])->name('lessons.preview');
    Route::post('/lessons/{lesson}/move', [Admin\LessonController::class, 'move'])->name('lessons.move');
    Route::delete('/lessons/{lesson}', [Admin\LessonController::class, 'destroy'])->name('lessons.destroy');

    Route::get('/members', [Admin\MemberController::class, 'index'])->name('members.index');
    Route::get('/members/{user}', [Admin\MemberController::class, 'show'])->name('members.show');
    Route::post('/members/{user}/grant', [Admin\MemberController::class, 'grant'])->name('members.grant');
    Route::post('/members/{user}/end', [Admin\MemberController::class, 'end'])->name('members.end');
    Route::post('/members/{user}/role', [Admin\MemberController::class, 'toggleRole'])->name('members.role');

    Route::get('/plans', [Admin\PlanController::class, 'index'])->name('plans.index');
    Route::put('/plans/{plan}', [Admin\PlanController::class, 'update'])->name('plans.update');
});
