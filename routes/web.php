<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\TeamRequest;
use App\Http\Controllers\Admin\TeamManagementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TodoController;
use App\Http\Controllers\Admin\WebsiteSettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CVController;
use App\Http\Controllers\Admin\NotificationController;


// FrontEnd
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/project/{slug}',  'project')->name('project.view');
    Route::POST('/contact-us', 'submit')->name('contact.submit')->middleware('throttle:contact');
    Route::POST('/contact-me',  'contactMe')->name('email.submit')->middleware('throttle:contact');
});

Route::controller(TeamController::class)->group(function () {
    Route::get('/team/{slug}', 'index')->name('team');
    Route::post('join/request', 'joinreq')->name('team.request')->middleware('throttle:forms');
});


Route::controller(CVController::class)->group(function () {
    Route::get('/cv/devhome/{slug}', 'generateDevHome')->name('cv.devhome');

    Route::get('/cv/custom/{slug}', 'downloadCustom')->name('cv.custom');
});













// Admin Side
Route::get('/login', [AdminController::class, 'loginPageView'])->name('login');

Route::post('/login', [AdminController::class, 'login'])->name('login.post')->middleware('throttle:login');
Route::get('/logout', [AdminController::class, 'logout'])->name('logout');




Route::middleware('auth')->group(function () {

    Route::post('/project-image-upload', [HomeController::class, 'uploadEditorImage'])
        ->name('project.image.upload')
        ->middleware(['section:projects', 'throttle:uploads']);

    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::post('/profile-update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/add-project', [ProjectController::class, 'index'])->name('add.project')->middleware('section:projects');

    Route::post('/store-project', [ProjectController::class, 'store'])->name('store.project')->middleware('section:projects');

    Route::get('/edit-project/{slug}', [ProjectController::class, 'edit'])->name('edit.project')->middleware('section:projects');

    Route::post('/update-project/{slug}', [ProjectController::class, 'update'])->name('update.project')->middleware('section:projects');

    Route::get('/project-list', [ProjectController::class, 'list'])->name('list.project')->middleware('section:projects');

    Route::post('/project-delete/{slug}', [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('section:projects');

    Route::get('/website-settings', [WebsiteSettingsController::class, 'index'])->name('website.settings')->middleware('section:website_settings');
    Route::post('/website/update', [WebsiteSettingsController::class, 'update'])->name('website.update')->middleware('section:website_settings');

    Route::get('/email-list', [EmailController::class, 'index'])->name('email.list')->middleware('section:email_list');

    Route::get('/website-contacts', [AdminController::class, 'contacts'])->name('website.contacts')->middleware('section:website_contacts');

    // Legacy dev-management URLs → Team Management (All Members).
    Route::get('/add-dev', fn () => redirect()->route('team.members.create'))->name('add.dev');
    Route::get('/dev-list', fn () => redirect()->route('team.members'))->name('dev.list');

    Route::get('/todo', [TodoController::class, 'index'])->name('todo')->middleware('section:todo');
    Route::post('/todo/store', [TodoController::class, 'store'])->name('todo.store')->middleware('section:todo');
    Route::delete('/todo/delete/{id}', [TodoController::class, 'destroy'])->name('todo.delete')->middleware('section:todo');

    Route::get('/join-list', [TeamRequest::class, 'index'])->name('join.list')->middleware('section:join_requests');
    Route::delete('/join-delete/{id}', [TeamRequest::class, 'destroy'])->name('join.delete')->middleware('section:join_requests');

    // Team Management (section:team_management; position writes are
    // additionally restricted to the super admin inside the controller).
    Route::middleware('section:team_management')->group(function () {
        Route::get('/positions', [TeamManagementController::class, 'positions'])->name('team.positions');
        Route::post('/positions', [TeamManagementController::class, 'storePosition'])->name('team.positions.store');
        Route::post('/positions/{position}/update', [TeamManagementController::class, 'updatePosition'])->name('team.positions.update');
        Route::post('/positions/{position}/delete', [TeamManagementController::class, 'destroyPosition'])->name('team.positions.delete');

        Route::get('/teams', [TeamManagementController::class, 'teams'])->name('team.teams');
        Route::post('/teams', [TeamManagementController::class, 'storeTeam'])->name('team.teams.store');
        Route::post('/teams/{team}/update', [TeamManagementController::class, 'updateTeam'])->name('team.teams.update');
        Route::post('/teams/{team}/delete', [TeamManagementController::class, 'destroyTeam'])->name('team.teams.delete');
        Route::post('/teams/{team}/members', [TeamManagementController::class, 'addMember'])->name('team.teams.members.add');
        Route::post('/teams/{team}/members/{userId}/remove', [TeamManagementController::class, 'removeMember'])->name('team.teams.members.remove');

        // All Members: list + filters (view: section holders; writes: super admin).
        // NOTE: {userId} not {user} — User's route key is slug, but all
        // management URLs use numeric IDs.
        Route::get('/members', [TeamManagementController::class, 'members'])->name('team.members');
        Route::get('/members/create', [TeamManagementController::class, 'createMember'])->name('team.members.create');
        Route::post('/members', [TeamManagementController::class, 'storeMember'])->name('team.members.store');
        Route::get('/members/{userId}/edit', [TeamManagementController::class, 'editMember'])->name('team.members.edit');
        Route::post('/members/{userId}/update', [TeamManagementController::class, 'updateMember'])->name('team.members.update');
        Route::post('/members/{userId}/delete', [TeamManagementController::class, 'deleteMember'])->name('team.members.delete');
        Route::post('/members/{userId}/restore', [TeamManagementController::class, 'restoreMember'])->name('team.members.restore');
    });

    Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-read');

    // TEMP DEBUG ROUTE - remove after fixing
    Route::get('/debug-dashboard', function () {
        try {
            $service = app(App\Services\DashboardService::class);
            $stats = $service->getStats(auth()->user());
            return response()->json(['success' => true, 'stats_keys' => array_keys($stats)]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    })->middleware('auth');
});
