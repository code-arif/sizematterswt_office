<?php

use App\Http\Controllers\Web\Admin\Ad\AdvertisementController;
use App\Http\Controllers\Web\Admin\Auth\AdminProfileController;
use App\Http\Controllers\Web\Admin\Contact\AdminChattingController;
use App\Http\Controllers\Web\Admin\Contact\AdminMailingController;
use App\Http\Controllers\Web\Admin\Dashboard\AdminDashboardController;
use App\Http\Controllers\Web\Admin\Event\EventController;
use App\Http\Controllers\Web\Admin\Firm\FarmController;
use App\Http\Controllers\Web\Admin\PrivacyandTerms\PrivacyPolicyController;
use App\Http\Controllers\Web\Admin\PrivacyandTerms\TermsConditionsController;
use App\Http\Controllers\Web\Admin\Ranches\RanchController;
use App\Http\Controllers\Web\Admin\Cms\SupportContentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdminDashboardController::class, 'index'])->name('show.admin.dashboard'); // show admin dashboard

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::prefix('profile')->name('admin.profile.')->group(function () {
    Route::get('/', [AdminProfileController::class, 'index'])->name('index'); // Show profile page
    Route::post('/general', [AdminProfileController::class, 'updateGeneral'])->name('general.update'); // Update general info (name, bio, address, phone)
    Route::post('/avatar', [AdminProfileController::class, 'updateAvatar'])->name('avatar.update'); // Upload new avatar
    Route::delete('/avatar', [AdminProfileController::class, 'deleteAvatar'])->name('avatar.delete');  // Remove avatar
    Route::post('/password', [AdminProfileController::class, 'updatePassword'])->name('password.update'); // Change password
    Route::post('/cover', [AdminProfileController::class, 'updateCover'])->name('cover.update');  // Upload cover photo
});

/*
|--------------------------------------------------------------------------
| Chatting
|--------------------------------------------------------------------------
*/
Route::prefix('chat')->name('admin.chat.')->group(function () {
    Route::get('/', [AdminChattingController::class, 'index'])->name('index');
});

/*
|--------------------------------------------------------------------------
| Mailing
|--------------------------------------------------------------------------
*/
Route::prefix('mail')->name('admin.mail.')->group(function () {
    Route::get('/', [AdminMailingController::class, 'index'])->name('index');
});

/*
|--------------------------------------------------------------------------
| Firm Manage
|--------------------------------------------------------------------------
*/
Route::prefix('farms')->name('admin.farms.')->group(function () {

    // Blade listing page
    Route::get('/', [FarmController::class, 'index'])->name('index');

    // Yajra DataTable JSON feed
    Route::get('/datatable', [FarmController::class, 'datatable'])->name('datatable');

    // Create / store
    Route::get('/create', [FarmController::class, 'create'])->name('create');
    Route::post('/store', [FarmController::class, 'store'])->name('store');

    // Show / edit / update / delete
    Route::get('/{farm}', [FarmController::class, 'show'])->name('show');
    Route::get('/{farm}/edit', [FarmController::class, 'edit'])->name('edit');
    Route::post('/{farm}', [FarmController::class, 'update'])->name('update');
    Route::delete('/{farm}', [FarmController::class, 'destroy'])->name('destroy');

    // Toggles
    Route::patch('/{farm}/toggle-status', [FarmController::class, 'toggleStatus'])->name('toggle-status');
    Route::patch('/{farm}/toggle-featured', [FarmController::class, 'toggleFeatured'])->name('toggle-featured');
    Route::delete('/{farm}/media/{mediaId}', [FarmController::class, 'removeMedia'])->name('media.delete');
});

/*
|--------------------------------------------------------------------------
| Ranches Manage
|--------------------------------------------------------------------------
*/
Route::prefix('ranches')->name('admin.ranches.')->group(function () {
    Route::get('/', [RanchController::class, 'index'])->name('index');
    Route::get('/datatable', [RanchController::class, 'datatable'])->name('datatable');
    Route::get('/create', [RanchController::class, 'create'])->name('create');
    Route::post('/', [RanchController::class, 'store'])->name('store');
    Route::get('/{ranch}', [RanchController::class, 'show'])->name('show');
    Route::get('/{ranch}/edit', [RanchController::class, 'edit'])->name('edit');
    Route::post('/{ranch}', [RanchController::class, 'update'])->name('update');
    Route::delete('/{ranch}', [RanchController::class, 'destroy'])->name('destroy');
    Route::patch('/{ranch}/toggle-status', [RanchController::class, 'toggleStatus'])->name('toggle-status');
    Route::patch('/{ranch}/toggle-featured', [RanchController::class, 'toggleFeatured'])->name('toggle-featured');
    Route::delete('/{ranch}/media/{mediaId}', [RanchController::class, 'removeMedia'])->name('media.delete');
});


/*
|--------------------------------------------------------------------------
| Event  Manage
|--------------------------------------------------------------------------
*/
Route::prefix('events')->name('admin.events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/datatable', [EventController::class, 'datatable'])->name('datatable');
    Route::get('/create', [EventController::class, 'create'])->name('create');
    Route::post('/', [EventController::class, 'store'])->name('store');
    Route::get('/{event}', [EventController::class, 'show'])->name('show');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [EventController::class, 'update'])->name('update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
    Route::patch('/{event}/toggle-status', [EventController::class, 'toggleStatus'])->name('toggle-status');
    Route::delete('/{event}/media/{mediaId}', [EventController::class, 'removeMedia'])->name('media.delete');
});

Route::prefix('advertisements')->name('admin.advertisements.')->group(function () {

    // Listing page (blade)
    Route::get('/', [AdvertisementController::class, 'index'])->name('index');

    // Yajra DataTable JSON
    Route::get('/datatable', [AdvertisementController::class, 'datatable'])->name('datatable');

    // Create / Store
    Route::get('/create', [AdvertisementController::class, 'create'])->name('create');
    Route::post('/', [AdvertisementController::class, 'store'])->name('store');

    // Show / Edit / Update / Delete
    Route::get('/{advertisement}', [AdvertisementController::class, 'show'])->name('show');
    Route::get('/{advertisement}/edit', [AdvertisementController::class, 'edit'])->name('edit');
    Route::post('/{advertisement}', [AdvertisementController::class, 'update'])->name('update');
    Route::delete('/{advertisement}', [AdvertisementController::class, 'destroy'])->name('destroy');

    // Toggle status
    Route::patch('/{advertisement}/toggle-status', [AdvertisementController::class, 'toggleStatus'])->name('toggle-status');
});

/*
|--------------------------------------------------------------------------
| Privacy & Terms
|--------------------------------------------------------------------------
*/

// Privacy Policy
Route::prefix('privacy-policy')->name('admin.privacy-policy.')->group(function () {
    Route::get('/', [PrivacyPolicyController::class, 'index'])->name('index');
    Route::post('/', [PrivacyPolicyController::class, 'store'])->name('store');
    Route::get('/edit', [PrivacyPolicyController::class, 'edit'])->name('edit');
    Route::patch('/{id}', [PrivacyPolicyController::class, 'update'])->name('update');
});

// Terms & Conditions
Route::prefix('terms-conditions')->name('admin.terms-conditions.')->group(function () {
    Route::get('/', [TermsConditionsController::class, 'index'])->name('index');
    Route::post('/', [TermsConditionsController::class, 'store'])->name('store');
    Route::get('/edit', [TermsConditionsController::class, 'edit'])->name('edit');
    Route::patch('/{id}', [TermsConditionsController::class, 'update'])->name('update');
});

/*
|--------------------------------------------------------------------------
| Support Content (CMS)
|--------------------------------------------------------------------------
*/



Route::prefix('support-content')->name('admin.support-content.')->group(function () {
    Route::get('/', [SupportContentController::class, 'index'])->name('index');
    Route::post('/', [SupportContentController::class, 'store'])->name('store');
    Route::get('/edit', [SupportContentController::class, 'edit'])->name('edit');
    Route::patch('/{id}', [SupportContentController::class, 'update'])->name('update');
});

