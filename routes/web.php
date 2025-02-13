<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TodoController;
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
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\LinkedUserController;
use App\Http\Controllers\What3WordsController;
use App\Http\Controllers\APIRequestsController;
use App\Http\Controllers\BandJourneyController;
use App\Http\Controllers\OtherServiceController;
use App\Http\Controllers\VenueJourneyController;
use App\Http\Controllers\DesignerJourneyController;
use App\Http\Controllers\PromoterJourneyController;
use App\Http\Controllers\PhotographerJourneyController;
use App\Http\Controllers\VideographerJourneyController;


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
Route::post('/venues/filter', [VenueController::class, 'filter'])->name('venues.filter');
Route::get('/venues/filterByCoordinates', [VenueController::class, 'filterByCoordinates'])
    ->name('venues.filterByCoordinates');
Route::post('/venues/{slug}/submitReview', [VenueController::class, 'submitVenueReview'])->name('submit-venue-review');
Route::get('/venues/{slug}', [VenueController::class, 'show'])->name('venue');
Route::get('/promoter-suggestion', [VenueController::class, 'suggestPromoters'])->name('suggestPromoters');

Route::get('/promoters', [PromoterController::class, 'index'])->name('promoters');
Route::post('/promoters/filter', [PromoterController::class, 'filter'])->name('promoters.filter');
Route::get('/promoters/{slug}', [PromoterController::class, 'show'])->name('promoter');
Route::post('/promoters/{slug}/submitReview', [PromoterController::class, 'submitPromoterReview'])->name('submit-promoter-review');

Route::get('/services', [OtherServiceController::class, 'index'])->name('other');
Route::post('/services/{serviceType}/filter', [OtherServiceController::class, 'filterCheckboxesSearch'])->name('other.filterCheckboxesSearch');
Route::get('/services/{serviceType}', [OtherServiceController::class, 'showGroup'])->name('singleServiceGroup');
Route::get('/services/{serviceType}/{name}', [OtherServiceController::class, 'show'])->name('singleService');
Route::post('/services/{serviceType}/{name}/submitReview', [OtherServiceController::class, 'submitReview'])->name('submit-single-service-review');

// Gig Guide
Route::get('/gig-guide', [GigGuideController::class, 'index'])->name('gig-guide');
Route::get('/gigs/filter', [GigGuideController::class, 'filterGigs'])->name('gigs.filter');
Route::view('/privacy-policy', 'privacy-policy');
Route::view('/about', 'about');
Route::view('/credits', 'credits');
Route::view('/contact', 'contact');

Route::get('/events', [EventController::class, 'getPublicEvents'])->name('public-events');
Route::get('/events/{eventId}', [EventController::class, 'getSinglePublicEvent'])->name('public-event');


Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // Main Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Dynamic Dashboard Routing
    Route::prefix('/dashboard')->group(function () {
        Route::get('/{dashboardType}', function ($dashboardType) {
            $controllerName = ucfirst($dashboardType) . 'DashboardController';
            $controllerClass = "App\\Http\\Controllers\\$controllerName";

            if (class_exists($controllerClass)) {
                return app($controllerClass)->index($dashboardType);
            }

            abort(404);
        })->name('dashboard');
    });

    Route::post('/dashboard/notes/store-note', [DashboardController::class, 'storeNewNote'])->name('dashboard.store-new-note');

    // First Time User Routes
    // Artist Journey
    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/band-journey', [BandJourneyController::class, 'index'])->name('band.journey');
        Route::get('/band-search', [BandJourneyController::class, 'search'])->name('band.search');
        Route::post('/band-journey/join/{id}', [BandJourneyController::class, 'joinBand'])->name('band.join');
        Route::post('/band-journey/create', [BandJourneyController::class, 'createBand'])->name('band.store');
    });

    // Promoter Journey
    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/promoter-journey', [PromoterJourneyController::class, 'index'])->name('promoter.journey');
        Route::get('/promoter-search', [PromoterJourneyController::class, 'search'])->name('promoter.search');
        Route::post('/promoter-journey/join/{id}', [PromoterJourneyController::class, 'joinPromoter'])->name('promoter.join');
        Route::post('/promoter-journey/create', [PromoterJourneyController::class, 'createPromoter'])->name('promoter.store');
    });

    // Venue Journey
    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/venue-journey', [VenueJourneyController::class, 'index'])->name('venue.journey');
        Route::get('/venue-search', [VenueJourneyController::class, 'searchVenue'])->name('venue.search');
        Route::post('/venue-journey/join/{id}', [VenueJourneyController::class, 'joinVenue'])->name('venue.link');
        Route::post('/venue/create', [VenueJourneyController::class, 'createVenue'])->name('venue.store');
    });

    // Photographer Journey
    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/photographer-journey', [PhotographerJourneyController::class, 'index'])->name('photographer.journey');
        Route::get('/photographer-search', [PhotographerJourneyController::class, 'searchPhotographer'])->name('photographer.search');
        Route::post('/photographer-journey/join/{id}', [PhotographerJourneyController::class, 'joinPhotographer'])->name('photographer.link');
        Route::post('/photographer/create', [PhotographerJourneyController::class, 'createPhotographer'])->name('photographer.store');
    });

    // Designer Journey
    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/designer-journey', [DesignerJourneyController::class, 'index'])->name('designer.journey');
        Route::get('/designer-search', [DesignerJourneyController::class, 'search'])->name('designer.search');
        Route::post('/designer-journey/join/{id}', [DesignerJourneyController::class, 'joinDesigner'])->name('designer.join');
        Route::post('/designer-journey/create', [DesignerJourneyController::class, 'createDesigner'])->name('designer.store');
    });

    // Videographer Journey
    Route::prefix('/{dashboardType}')->middleware(['auth'])->group(function () {
        Route::get('/videographer-journey', [VideographerJourneyController::class, 'index'])->name('videographer.journey');
        Route::get('/videographer-search', [VideographerJourneyController::class, 'search'])->name('videographer.search');
        Route::post('/videographer-journey/join/{id}', [VideographerJourneyController::class, 'joinVideographer'])->name('videographer.join');
        Route::post('/videographer-journey/create', [VideographerJourneyController::class, 'createVideographer'])->name('videographer.store');
    });

    // Finances
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/finances', [FinanceController::class, 'showFinances'])->name('admin.dashboard.show-finances');
        Route::get('/finances/data', [FinanceController::class, 'getFinancesData'])->name('admin.dashboard.get-finances-data');
        Route::get('/finances/new-budget', [FinanceController::class, 'createFinance'])->name('admin.dashboard.create-new-finance');
        Route::post('/finances/save-budget', [FinanceController::class, 'storeFinance'])->name('admin.dashboard.store-new-finance');
        Route::post('/finances/export', [FinanceController::class, 'exportFinances'])->name('admin.dashboard.finances.export');
        Route::get('/finances/{id}', [FinanceController::class, 'showSingleFinance'])->name('admin.dashboard.show-finance');
        Route::get('/finances/{id}/edit', [FinanceController::class, 'editFinance'])->name('admin.dashboard.edit-finance');
        Route::put('/finances/{id}/update', [FinanceController::class, 'updateFinance'])->name('admin.dashboard.update-finance');
        Route::post('/finances/{finance}/export', [FinanceController::class, 'exportSingleFinance'])->name('admin.dashboard.export-finance');
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
        Route::get('/reviews/{filter?}', [ReviewController::class, 'getReviews'])->name('admin.dashboard.get-reviews');
        Route::get('/filtered-reviews/{filter?}', [ReviewController::class, 'fetchReviews'])->name('admin.dashboard.fetch-reviews');
        Route::get('/reviews/pending', [ReviewController::class, 'showPendingReviews'])->name('admin.dashboard.show-pending-reviews');
        Route::get('/reviews/all', [ReviewController::class, 'showAllReviews'])->name('admin.dashboard.show-all-reviews');
        Route::post('/reviews/{reviewId}/approve', [ReviewController::class, 'approveReview'])->name('admin.dashboard.reviews.approve');
        Route::post('/reviews/{reviewId}/show', [ReviewController::class, 'displayReview'])->name('admin.dashboard.reviews.show');
        Route::post('/reviews/{reviewId}/hide', [ReviewController::class, 'hideReview'])->name('admin.dashboard.reviews.hide');
        Route::delete('/reviews/{reviewId}/delete', [ReviewController::class, 'deleteReview'])->name('admin.dashboard.reviews.delete');
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
        Route::post('/events/{id}/add-to-calendar', [CalendarController::class, 'addEventToInternalCalendar'])->name('admin.dashboard.add-event-to-calendar');
        Route::get('/events/{user}/check-linked-calendars', [CalendarController::class, 'checkLinkedCalendars']);
        Route::delete('/events/{id}/delete', [EventController::class, 'deleteEvent'])->name('admin.dashboard.delete-event');
        Route::get('/events/promoters/search', [APIRequestsController::class, 'searchPromoters']);
        Route::post('/events/promoters/create', [APIRequestsController::class, 'createPromoter']);
        Route::get('/events/bands/search', [APIRequestsController::class, 'searchBands']);
        Route::post('/events/bands/create', [APIRequestsController::class, 'createBand']);
        Route::get('/events/venues/search', [APIRequestsController::class, 'searchVenues']);
        Route::post('/events/venues/create', [APIRequestsController::class, 'createVenue']);
    });

    // To-Do List
    Route::prefix('/dashboard/{dashboardType}/todo-list')->group(function () {
        // View Routes
        Route::get('/', [TodoController::class, 'showTodos'])->name('admin.dashboard.todo-list');
        Route::get('/completed', [TodoController::class, 'showCompletedTodoItems'])->name('admin.dashboard.completed-todos');
        Route::get('/uncompleted', [TodoController::class, 'showUncompletedTodoItems'])->name('admin.dashboard.uncompleted-todos');
        Route::get('/load-more', [TodoController::class, 'loadMoreTodos'])->name('admin.dashboard.load-more-todos');

        Route::post('/new', [TodoController::class, 'newTodoItem'])->name('admin.dashboard.new-todo');
        Route::post('/{id}/complete', [TodoController::class, 'completeTodoItem'])->name('admin.dashboard.complete-todo');
        Route::post('/{id}/uncomplete', [TodoController::class, 'uncompleteTodoItem'])->name('admin.dashboard.uncomplete-todo');
        Route::delete('/{id}', [TodoController::class, 'deleteTodoItem'])->name('admin.dashboard.delete-todo');

        // Status Check Routes
        Route::get('/has-completed', [TodoController::class, 'hasCompletedTodos'])->name('admin.dashboard.has-completed-todos');
        Route::get('/has-uncompleted', [TodoController::class, 'hasUncompletedTodos'])->name('admin.dashboard.has-cunompleted-todos');
    });

    // Jobs
    Route::prefix('/dashboard/{dashboardType}')->group(function () {
        Route::get('/jobs', [JobsController::class, 'showJobs'])->name('admin.dashboard.jobs');
        Route::get('/jobs/new', [JobsController::class, 'newJob'])->name('admin.dashboard.jobs.create');
        Route::post('/jobs/store', [JobsController::class, 'storeJob'])->name('admin.dashboard.jobs.store');
        Route::get('/jobs/{job}', [JobsController::class, 'viewJob'])->name('admin.dashboard.jobs.view');
        Route::get('/jobs/{job}/edit', [JobsController::class, 'editJob'])->name('admin.dashboard.jobs.edit');
        Route::put('/jobs/{job}/update', [JobsController::class, 'updateJob'])->name('admin.dashboard.jobs.update');
        Route::delete('/jobs/{job}/delete', [JobsController::class, 'deleteJob'])->name('admin.dashboard.jobs.delete');
        Route::get('/jobs/{job}/download', [JobsController::class, 'downloadFile'])->name('admin.dashboard.jobs.download');
        Route::post('/jobs/{job}/complete', [JobsController::class, 'completeJob'])->name('admin.dashboard.jobs.complete');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    // What3Words
    Route::post('/what3words/suggest', [What3WordsController::class, 'suggest'])->name('what3words.suggest');

    // More specific routes
    // Profile Updates
    Route::put('/profile/{dashboardType}/promoter-profile-update/{user}', [ProfileController::class, 'updatePromoter'])->name('promoter.update');
    Route::put('/profile/{dashboardType}/venue-profile-update/{user}', [ProfileController::class, 'updateVenue'])->name('venue.update');
    Route::put('/profile/{dashboardType}/band-profile-update/{user}', [ProfileController::class, 'updateBand'])->name('artist.update');
    Route::put('/profile/{dashboardType}/photographer-profile-update/{user}', [ProfileController::class, 'updatePhotographer'])->name('photographer.update');
    Route::put('/profile/{dashboardType}/standard-user-update/{user}', [ProfileController::class, 'updateStandardUser'])->name('standard-user.update');
    Route::put('/profile/{dashboardType}/designer-user-update/{user}', [ProfileController::class, 'updateDesigner'])->name('designer.update');
    Route::put('/profile/{dashboardType}/videographer-user-update/{user}', [ProfileController::class, 'updateVideographer'])->name('videographer.update');
    Route::post('/profile/{dashboardType}/portfolio-image-upload', [ProfileController::class, 'uploadPortfolioImages'])->name('portfolio.upload');
    Route::get('/profile/{dashboardType}/settings', [ProfileController::class, 'settings'])->name('settings.index');
    Route::post('/profile/{dashboardType}/photographer-environment-types', [ProfileController::class, 'updateEnvironmentTypes'])->name('photographer.environment-types');
    Route::post('/profile/{dashboardType}/save-genres', [ProfileController::class, 'saveGenres'])->name('save-genres');
    Route::post('/profile/{dashboardType}/save-band-types', [ProfileController::class, 'saveBandTypes'])->name('save-band-types');
    Route::put('/profile/{dashboardType}/{user}/portfolio-save', [ProfileController::class, 'savePortfolio'])->name('portfolio.save');
    Route::post('/profile/{dashboardType}/{user}/add-role', [ProfileController::class, 'addRole'])->name('profile.add-role');
    Route::post('/profile/{dashboardType}/{user}/edit-role', [ProfileController::class, 'editRole'])->name('profile.edit-role');
    Route::delete('/profile/{dashboardType}/{user}/delete-role', [ProfileController::class, 'deleteRole'])->name('profile.delete-role');
    Route::post('/profile/{dashboardType}/{id}/packages/update', [APIRequestsController::class, 'updatePackages'])->name('settings.updatePackages');
    Route::post('/profile/{dashboardType}/settings/update', [APIRequestsController::class, 'updateModule'])->name('settings.updateModule');
    Route::get('/dashboard/{$dashboardType}/finances', [FinanceController::class, 'getFinanceData']);
    Route::get('/{dashboardType}/jobs/search-clients', [APIRequestsController::class, 'searchClients']);
    Route::post('/profile/{dashboardType}/{id}/update-api-keys', [APIRequestsController::class, 'updateAPI']);
    Route::post('/profile/{dashboardType}/{id}/save-styles-and-print', [APIRequestsController::class, 'updateStylesAndPrint'])->name('profile.styles-and-print');
    Route::post('/profile/{dashboardType}/communications/update', [APIRequestsController::class, 'updateCommunications'])->name('settings.updateCommunications');
    Route::post('/profile/{dashboardType}/{id}/leave-service', [APIRequestsController::class, 'leaveService'])->name('settings.deleteModule');


    Route::middleware(['auth'])->group(function () {
        Route::post('/integrations/store', [IntegrationController::class, 'store'])
            ->name('integrations.store');
    });

    // API Key Routes
    // Route::put('/profile/{dashboardType}/update-api-key', [ProfileController::class, 'updateAPI'])->name('profile.update-api');
    // Calendar-specific routes
    Route::get('/profile/events/{user}/apple/sync', [CalendarController::class, 'syncAllEventsToAppleCalendar'])->name('apple.sync');
    Route::get('/profile/{dashboardType}/events/{user}', [APIRequestsController::class, 'getUserCalendarEvents']);

    // General profile routes
    Route::get('/profile/{dashboardType}/{id}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{dashboardType}/{user}', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Google calendar routes
    Route::get('/dashboard/{dashboardType}/auth/google', [CalendarController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [CalendarController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::post('/dashboard/{dashboardType}/google/sync', [CalendarController::class, 'syncGoogleCalendar'])->name('google.sync');
    Route::post('/dashboard/{dashboardType}/google/unlink', [CalendarController::class, 'unlinkGoogle'])->name('google.unlink');
});

require __DIR__ . '/auth.php';