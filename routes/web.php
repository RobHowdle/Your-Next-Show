<?php

use App\Models\OtherService;
use App\Services\What3WordsService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GigGuideController;
use App\Http\Controllers\PromoterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LinkedUserController;
use App\Http\Controllers\What3WordsController;
use App\Http\Controllers\APIRequestsController;
use App\Http\Controllers\BandJourneyController;
use App\Http\Controllers\OtherServiceController;
use App\Http\Controllers\VenueJourneyController;
use App\Http\Controllers\BandDashboardController;
use App\Http\Controllers\VenueDashboardController;
use App\Http\Controllers\DesignerJourneyController;
use App\Http\Controllers\PromoterDashboardController;
use App\Http\Controllers\PhotographerJourneyController;


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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/venues', [VenueController::class, 'index'])->name('venues');
Route::get('/venues/filter', [VenueController::class, 'filterCheckboxesSearch'])->name('venues.filterCheckboxesSearch');
Route::get('/venues/filterByCoordinates', [VenueController::class, 'filterByCoordinates'])
    ->name('venues.filterByCoordinates');
Route::post('/venues/{id}/submitReview', [VenueController::class, 'submitVenueReview'])->name('submit-venue-review');
Route::get('/venues/{id}', [VenueController::class, 'show'])->name('venue');
Route::get('/promoter-suggestion', [VenueController::class, 'suggestPromoters'])->name('suggestPromoters');

Route::get('/promoters', [PromoterController::class, 'index'])->name('promoters');
Route::get('/promoters/filter', [PromoterController::class, 'filterCheckboxesSearch'])->name('promoters.filterCheckboxesSearch');
Route::get('/promoters/{id}', [PromoterController::class, 'show'])->name('promoter');
Route::post('/promoters/{id}/submitReview', [PromoterController::class, 'submitPromoterReview'])->name('submit-promoter-review');

Route::get('/other', [OtherServiceController::class, 'index'])->name('other');
Route::get('/other/{serviceName}', [OtherServiceController::class, 'showGroup'])->name('singleServiceGroup');
Route::get('/other/{serviceName}/{serviceId}', [OtherServiceController::class, 'show'])->name('singleService');
Route::get('/other/filter', [OtherServiceController::class, 'filterCheckboxesSearch'])->name('other.filterCheckboxesSearch');

Route::get('/gig-guide', [GigGuideController::class, 'showGigGuide'])->name('gig-guide');
Route::get('/gigs/filter', [GigGuideController::class, 'filterGigs'])->name('gigs.filter');
Route::view('/privacy-policy', 'privacy-policy');

Route::get('/events', [EventController::class, 'getPublicEvents'])->name('public-events');
Route::get('/events/{eventId}', [EventController::class, 'getSinglePublicEvent'])->name('public-event');


Route::middleware(['auth', 'web', 'verified'])->group(function () {
    // Dashboards - Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Dashboards - Role Based Dashboards
    Route::prefix('/dashboard')->group(function () {
        Route::get('/{dashboardType}', function ($dashboardType) {
            // Determine the appropriate controller based on the dashboard type
            $controllerName = ucfirst($dashboardType) . 'DashboardController';

            // Check if the controller class exists
            if (class_exists("App\\Http\\Controllers\\$controllerName")) {
                // Create an instance of the controller and call the index method
                return app("App\\Http\\Controllers\\$controllerName")->index($dashboardType);
            }

            // Handle the case where the controller does not exist
            abort(404);
        })->name('dashboard');
    });

    Route::post('/dashboard/notes/store-note', [DashboardController::class, 'storeNewNote'])->name('dashboard.store-new-note');

    // First Time User Routes
    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/band-journey', [BandJourneyController::class, 'index'])->name('band.journey');
        Route::get('/band-search', [BandJourneyController::class, 'search'])->name('band.search');
        Route::post('/band-journey/join/{id}', [BandJourneyController::class, 'joinBand'])->name('band.join');
        Route::post('/band-journey/create', [BandJourneyController::class, 'createBand'])->name('band.create');
    });

    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/users/search', [PromoterDashboardController::class, 'searchExistingPromoters'])->name('admin.dashboard.promoter.search');
        Route::post('/link', [PromoterDashboardController::class, 'linkToExistingPromoter'])->name('admin.dashboard.promoter.link');
        Route::post('/store', [PromoterDashboardController::class, 'storeNewPromoter'])->name('admin.dashboard.promoter.store');
    });

    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/venue-journey', [VenueJourneyController::class, 'index'])->name('venue.journey');
        Route::get('/venue-search', [VenueJourneyController::class, 'searchVenue'])->name('venue.search');
        Route::get('/venue-select', [VenueJourneyController::class, 'selectVenue'])->name('venue.select');
        Route::post('/venue-journey/link/{id}', [VenueJourneyController::class, 'linkVenue'])->name('venue.link');
        Route::post('/venue/create', [VenueJourneyController::class, 'createVenue'])->name('venue.store');
    });

    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/photographer-journey', [PhotographerJourneyController::class, 'index'])->name('photographer.journey');
        Route::get('/photographer-search', [PhotographerJourneyController::class, 'searchPhotographer'])->name('photographer.search');
        Route::get('/photographer-select', [PhotographerJourneyController::class, 'selectPhotogrpher'])->name('photographer.select');
        Route::post('/photographer-journey/link/{id}', [PhotographerJourneyController::class, 'linkPhotographer'])->name('photographer.link');
        Route::post('/photographer/create', [PhotographerJourneyController::class, 'createPhotographer'])->name('photographer.store');
    });

    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/designer-journey', [DesignerJourneyController::class, 'index'])->name('designer.journey');
        Route::get('/designer-search', [DesignerJourneyController::class, 'search'])->name('designer.search');
        Route::post('/designer-journey/join/{id}', [DesignerJourneyController::class, 'joinDesigner'])->name('designer.join');
        Route::post('/designer-journey/create', [DesignerJourneyController::class, 'createDesigner'])->name('designer.create');
    });

    // Finances
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/finances', [FinanceController::class, 'showFinances'])->name('admin.dashboard.show-finances');
        Route::get('/finances/new-budget', [FinanceController::class, 'createFinance'])->name('admin.dashboard.create-new-finance');
        Route::post('/finances/save-budget', [FinanceController::class, 'storeFinance'])->name('admin.dashboard.store-new-finance');
        Route::post('/finances/export', [FinanceController::class, 'exportFinances'])->name('admin.dashboard.finances.export');
        Route::get('/finances/{id}', [FinanceController::class, 'showSingleFinance'])->name('admin.dashboard.show-finance');
        Route::get('/finances/{id}/edit', [FinanceController::class, 'editFinance'])->name('admin.dashboard.edit-finance');
        Route::patch('/finances/{finance}', [FinanceController::class, 'updateFinance'])->name('admin.dashboard.update-finances');
        Route::post('/finances/{finance}', [FinanceController::class, 'exportSingleFinance'])->name('admin.dashboard.export-finance');
    });

    // Users
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/users', [LinkedUserController::class, 'showUsers'])->name('admin.dashboard.users');
        Route::get('/users/get', [LinkedUserController::class, 'getUsers'])->name('admin.dashboard.get-users');
        Route::get('/users/new-user', [LinkedUserController::class, 'newUser'])->name('admin.dashboard.new-user');
        Route::get('/users/search-users', [LinkedUserController::class, 'searchUsers'])->name('admin.dashboard.search-users');
        Route::post('/users/add-user/{id}', [LinkedUserController::class, 'linkUser'])->name('admin.dashboard.link-user');
        Route::delete('/users/delete-user/{id}', [LinkedUserController::class, 'deleteUser'])->name('admin.dashboard.delete-user');
    });

    // Notes
    //TODO - Finish
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/notes', [NoteController::class, 'showNotes'])->name('admin.dashboard.show-notes');
        Route::get('/note-items', [NoteController::class, 'getNotes'])->name('admin.dashboard.note-items');
        Route::post('/notes/new', [NoteController::class, 'newNoteItem'])->name('admin.dashboard.new-note-item');
        Route::post('/note-item/{id}/complete', [NoteController::class, 'completeNoteItem'])->name('admin.dashboard.complete-note');
        Route::post('/note-item/{id}/uncomplete', [NoteController::class, 'uncompleteNoteItem'])->name('admin.dashboard.uncomplete-note-item');
        Route::delete('/note-item/{id}', [NoteController::class, 'deleteNoteItem'])->name('admin.dashboard.delete-note');
        Route::get('/note-item/completed-notes', [NoteController::class, 'showCompletedNoteItems'])->name('admin.dashboard.completed-notes');
    });

    // Reviews
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/reviews/{filter?}', [ReviewController::class, 'getPromoterReviews'])->name('admin.dashboard.get-reviews');
        Route::get('/filtered-reviews/{filter?}', [ReviewController::class, 'fetchReviews'])->name('admin.dashboard.fetch-reviews');
        Route::get('/reviews/pending', [ReviewController::class, 'showPendingReviews'])->name('admin.dashboard.show-pending-reviews');
        Route::get('/reviews/all', [ReviewController::class, 'showAllReviews'])->name('admin.dashboard.show-all-reviews');
        Route::post('/approve-display/{reviewId}', [ReviewController::class, 'approveDisplayReview'])->name('admin.dashboard.approve-display-review');
        Route::post('/hide-display-review/{reviewId}', [ReviewController::class, 'hideReview'])->name('admin.dashboard.hide-display-review');
        Route::post('/approve/{reviewId}', [ReviewController::class, 'approveReview'])->name('admin.dashboard.approve-pending-review');
        Route::post('/unapprove-review/{reviewId}', [ReviewController::class, 'unapproveReview'])->name('admin.dashboard.unapprove-review');
        Route::delete('/delete-review/{reviewId}', [ReviewController::class, 'deleteReview'])->name('admin.dashboard.delete-review');
    });

    // Documents
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/documents', [DocumentController::class, 'index'])->name('admin.dashboard.documents.index');
        Route::get('/documents/new', [DocumentController::class, 'create'])->name('admin.dashboard.document.create');
        Route::get('/documents/{id}', [DocumentController::class, 'show'])->name('admin.dashboard.document.show');
        Route::get('/documents/{id}/edit', [DocumentController::class, 'edit'])->name('admin.dashboard.document.edit');
        Route::post('/document/file-upload', [DocumentController::class, 'fileUpload'])->name('admin.dashboard.document.file.upload');
        Route::post('/documents/store', [DocumentController::class, 'storeDocument'])->name('admin.dashboard.store-document');
        Route::put('/documents/{id}', [DocumentController::class, 'update'])->name('admin.dashboard.document.update');
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('admin.dashboard.document.delete');
        Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('admin.dashboard.document.download');
    });

    // Events
    // TODO - Finish
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/events', [EventController::class, 'showEvents'])->name('admin.dashboard.show-events');
        Route::get('/events/load-more-upcoming', [EventController::class, 'loadMoreUpcomingEvents'])->name('admin.dashboard.load-more-upcoming-events');
        Route::get('/events/load-more-past', [EventController::class, 'loadMorePastEvents'])->name('admin.dashboard.load-more-past-events');
        Route::get('/events/create-event', [EventController::class, 'createNewEvent'])->name('admin.dashboard.create-new-event');
        Route::post('/events/store-event', [EventController::class, 'storeNewEvent'])->name('admin.dashboard.store-new-event');
        Route::get('/events/search-venues', [EventController::class, 'selectVenue'])->name('admin.dashboard.search-venues');
        Route::get('/events/search-promoters', [EventController::class, 'selectPromoter'])->name('admin.dashboard.search-promoters');
        Route::get('/events/{id}', [EventController::class, 'showEvent'])->name('admin.dashboard.show-event');
        Route::get('/events/{id}/edit', [EventController::class, 'editEvent'])->name('admin.dashboard.edit-event');
        Route::put('/events/{id}/update', [EventController::class, 'updateEvent'])->name('admin.dashboard.update-event');
        Route::post('/events/{id}/add-to-calendar', [CalendarController::class, 'addEventToCalendar'])->name('admin.dashboard.add-event-to-calendar');
        Route::get('/events/{user}/check-linked-calendars', [CalendarController::class, 'checkLinkedCalendars']);
        Route::delete('/events/{id}/delete', [EventController::class, 'deleteEvent'])->name('admin.dashboard.delete-event');
    });

    // To-Do List
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/todo-list', [TodoController::class, 'showTodos'])->name('admin.dashboard.todo-list');
        Route::get('/todo-items', [TodoController::class, 'getTodos'])->name('admin.dashboard.todo-items');
        Route::post('/todo-list/new', [TodoController::class, 'newTodoItem'])->name('admin.dashboard.new-todo-item');
        Route::post('/todo-item/{id}/complete', [TodoController::class, 'completeTodoItem'])->name('admin.dashboard.complete-todo-item');
        Route::post('/todo-item/{id}/uncomplete', [TodoController::class, 'uncompleteTodoItem'])->name('admin.dashboard.uncomplete-todo-item');
        Route::delete('/todo-item/{id}', [TodoController::class, 'deleteTodoItem'])->name('admin.dashboard.delete-todo-item');
        Route::get('/todo-item/completed-items', [TodoController::class, 'showCompletedTodoItems'])->name('admin.dashboard.completed-todo-items');
    });

    // Jobs
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/jobs', [JobsController::class, 'showJobs'])->name('admin.dashboard.jobs');
        // Route::get('/job-items', [JobsController::class, 'getJobs'])->name('admin.dashboard.job-items');
        Route::get('/jobs/new', [JobsController::class, 'newJob'])->name('admin.dashboard.jobs.create');
        Route::post('/jobs/store', [JobsController::class, 'storeJob'])->name('admin.dashboard.jobs.store');
        Route::get('/job/{id}', [JobsController::class, 'viewJob'])->name('admin.dashboard.job.view');
        Route::put('/jobs/{id}/update', [JobsController::class, 'updateJob'])->name('admin.dashboard.jobs.update');
        Route::delete('/jobs/{id}/delete', [JobsController::class, 'deleteJob'])->name('admin.dashboard.jobs.delete');
        Route::get('/jobs/search-clients', [JobsController::class, 'searchClients'])->name('admin.dashboard.jobs.client-search');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    // What3Words
    Route::post('/what3words/suggest', [What3WordsController::class, 'suggest'])->name('what3words.suggest');

    // More specific routes
    // Profile Updates
    Route::put('/profile/{dashboardType}/promoter-profile-update/{user}', [ProfileController::class, 'updatePromoter'])->name('promoter.update');
    Route::put('/profile/{dashboardType}/venue-profile-update/{user}', [ProfileController::class, 'updateVenue'])->name('venue.update');
    Route::put('/profile/{dashboardType}/band-profile-update/{user}', [ProfileController::class, 'updateBand'])->name('band.update');
    Route::put('/profile/{dashboardType}/photographer-profile-update/{user}', [ProfileController::class, 'updatePhotographer'])->name('photographer.update');
    Route::put('/profile/{dashboardType}/standard-user-update/{user}', [ProfileController::class, 'updateStandardUser'])->name('standard-user.update');
    Route::put('/profile/{dashboardType}/designer-user-update/{user}', [ProfileController::class, 'updateDesigner'])->name('designer.update');
    Route::post('/profile/{dashboardType}/portfolio-image-upload', [ProfileController::class, 'uploadPortfolioImages'])->name('portfolio.upload');
    Route::get('/profile/{dashboardType}/settings', [ProfileController::class, 'settings'])->name('settings.index');
    Route::post('/profile/{dashboardType}/photographer-environment-types', [ProfileController::class, 'updateEnvironmentTypes'])->name('photographer.environment-types');
    Route::post('/profile/{dashboardType}/settings/update', [ProfileController::class, 'updateModule'])->name('settings.updateModule');
    Route::get('/profile/{dashboardType}/communications', [ProfileController::class, 'communications'])->name('communications.index');
    Route::post('/profile/{dashboardType}/communications/update', [ProfileController::class, 'updatePreferences'])->name('communications.updatePreferences');
    Route::post('/profile/{dashboardType}/save-genres', [ProfileController::class, 'saveGenres'])->name('save-genres');
    Route::post('/profile/{dashboardType}/save-band-types', [ProfileController::class, 'saveBandTypes'])->name('save-band-types');

    Route::put('/profile/{dashboardType}/{user}/portfolio-save', [ProfileController::class, 'savePortfolio'])->name('portfolio.save');
    Route::post('/profile/{dashboardType}/{user}/add-role', [ProfileController::class, 'addRole'])->name('profile.add-role');
    Route::post('/profile/{dashboardType}/{user}/edit-role', [ProfileController::class, 'editRole'])->name('profile.edit-role');
    Route::delete('/profile/{dashboardType}/{user}/delete-role', [ProfileController::class, 'deleteRole'])->name('profile.delete-role');

    // Calendar-specific routes
    Route::get('/profile/events/{user}/apple/sync', [CalendarController::class, 'syncAllEventsToAppleCalendar'])->name('apple.sync');
    Route::get('/profile/events/{user}', [APIRequestsController::class, 'getUserCalendarEvents']);

    // General profile routes
    Route::get('/profile/{dashboardType}/{id}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{dashboardType}/{user}', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Google calendar routes
    Route::get('auth/google', [CalendarController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('auth/google/callback', [CalendarController::class, 'handleGoogleCallback']);
    Route::post('google/sync', [CalendarController::class, 'syncGoogleCalendar'])->name('google.sync');
    Route::post('google/unlink', [CalendarController::class, 'unlinkGoogle'])->name('google.unlink');
});


require __DIR__ . '/auth.php';
