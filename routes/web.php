<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Artisan;

Route::get('/symlink', function () {
    Artisan::call('storage:link');
    return 'Le lien symbolique a été créé avec succès !';
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirection de la racine vers le dashboard
Route::get('/', function () {
    return view('landing');
});

// ──────────────────────────────────────────────
//  Routes d'Authentification
// ──────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    // Connexion
    Route::get('/signin',  [AuthController::class, 'signinForm'])->name('login');
    Route::post('/signin', [AuthController::class, 'signin'])->name('login.post');

    // Inscription
    Route::get('/signup',  [AuthController::class, 'signupForm'])->name('auth.signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('auth.signup.post');

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// ──────────────────────────────────────────────
//  Routes de Vérification d'Email
// ──────────────────────────────────────────────
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('onboarding.index');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Lien de vérification envoyé !');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ──────────────────────────────────────────────
//  Routes d'Onboarding (Configuration pas à pas)
// ──────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('index');
    Route::post('/step/{step}', [OnboardingController::class, 'saveStep'])->name('save-step');
    Route::get('/complete', [OnboardingController::class, 'complete'])->name('complete');
});

use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\ProjectionController;
use App\Http\Controllers\ParticipantController;

// ──────────────────────────────────────────────
//  Routes Participant (Public)
// ──────────────────────────────────────────────
Route::get('/join', [ParticipantController::class, 'joinForm'])->name('participant.join');
Route::post('/join', [ParticipantController::class, 'join'])->name('participant.join.post');
Route::get('/e/{code}', [ParticipantController::class, 'eventInterface'])->name('participant.event');
Route::post('/e/{code}/ask', [ParticipantController::class, 'storeQuestion'])->name('participant.ask');
Route::post('/q/{id}/vote', [ParticipantController::class, 'vote'])->name('participant.vote');
Route::post('/q/{id}/reply', [ParticipantController::class, 'storeReply'])->name('participant.reply');
Route::post('/e/{code}/raise-hand', [ParticipantController::class, 'raiseHand'])->name('participant.raise-hand');
Route::post('/e/{code}/lower-hand', [ParticipantController::class, 'lowerHand'])->name('participant.lower-hand');
Route::post('/e/{code}/heartbeat', [ParticipantController::class, 'heartbeat'])->name('participant.heartbeat');
Route::post('/e/{code}/typing', [ParticipantController::class, 'updateTyping'])->name('participant.typing');
Route::get('/e/{code}/active-participants', [ParticipantController::class, 'getActiveParticipants'])->name('participant.active-participants');

// ──────────────────────────────────────────────
//  Routes de Projection (Public/Régie)
// ──────────────────────────────────────────────
Route::get('/e/{code}/projection', [ProjectionController::class, 'index'])->name('projection.index');
Route::get('/e/{code}/projection/api', [ProjectionController::class, 'getAnswering'])->name('projection.api');

use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticsController;

use App\Http\Controllers\InsightsController;

use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PanelistController;

// ──────────────────────────────────────────────
//  Routes du Dashboard (Protégées)
// ──────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'onboarding.completed'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/',            [DashboardController::class, 'index'])->name('index');
    
    // Gestion des Événements (CRUD)
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/',                [EventController::class, 'index'])->name('index');
        Route::get('/create',          [EventController::class, 'create'])->name('create');
        Route::post('/',               [EventController::class, 'store'])->name('store');
        Route::get('/{id}',            [EventController::class, 'show'])->name('show');
        Route::get('/{id}/edit',       [EventController::class, 'edit'])->name('edit');
        Route::put('/{id}',            [EventController::class, 'update'])->name('update');
        Route::delete('/{id}',         [EventController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [EventController::class, 'toggleStatus'])->name('toggle-status');
        
        // Panelists
        Route::post('/{id}/panelists', [PanelistController::class, 'store'])->name('panelists.store');
        Route::put('/panelists/{panelistId}', [PanelistController::class, 'update'])->name('panelists.update');
        Route::delete('/panelists/{panelistId}', [PanelistController::class, 'destroy'])->name('panelists.destroy');
    });

    // Console de Modération
    Route::get('/event/{id}/moderation', [ModeratorController::class, 'index'])->name('moderator.index');
    Route::post('/question/{id}/status', [ModeratorController::class, 'updateStatus'])->name('moderator.status');
    Route::post('/question/{id}/edit',   [ModeratorController::class, 'updateContent'])->name('moderator.edit');
    Route::post('/question/{id}/reply',  [ModeratorController::class, 'storeReply'])->name('moderator.reply');
    Route::post('/hand/{id}/status',     [ModeratorController::class, 'updateHandStatus'])->name('moderator.hand-status');

    // Profil
    Route::get('/profile',          [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Statistiques
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');

    // IA Insights
    Route::prefix('insights')->name('insights.')->group(function () {
        Route::get('/',            [InsightsController::class, 'index'])->name('index');
        Route::get('/{id}',        [InsightsController::class, 'show'])->name('show');
        Route::get('/{id}/export', [InsightsController::class, 'export'])->name('export');
    });

    // Abonnement
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
});

// ──────────────────────────────────────────────
//  Routes Panéliste
// ──────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('panelist')->name('panelist.')->group(function () {
    Route::get('/', [PanelistController::class, 'index'])->name('index');
    Route::get('/join', [PanelistController::class, 'joinForm'])->name('join.form');
    Route::post('/join', [PanelistController::class, 'join'])->name('join');
    Route::get('/e/{code}', [PanelistController::class, 'dashboard'])->name('dashboard');
    Route::post('/e/{code}/upload', [PanelistController::class, 'upload'])->name('upload');
    Route::post('/e/{code}/ai-suggest', [PanelistController::class, 'aiSuggest'])->name('ai-suggest');
});
