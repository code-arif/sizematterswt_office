<?php

use App\Http\Controllers\Web\Admin\Auth\AdminProfileController;
use App\Http\Controllers\Web\Admin\Contact\AdminChattingController;
use App\Http\Controllers\Web\Admin\Contact\AdminMailingController;
use App\Http\Controllers\Web\Admin\Dashboard\AdminDashboardController;
use App\Http\Controllers\Web\Admin\Firm\FarmController;
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
    Route::get('/',[FarmController::class, 'index'])->name('index');

    // Yajra DataTable JSON feed
    Route::get('/datatable',[FarmController::class, 'datatable'])->name('datatable');

    // Create / store
    Route::get('/create',[FarmController::class, 'create'])->name('create');
    Route::post('/store',[FarmController::class, 'store'])->name('store');

    // Show / edit / update / delete
    Route::get('/{farm}',[FarmController::class, 'show'])->name('show');
    Route::get('/{farm}/edit',[FarmController::class, 'edit'])->name('edit');
    Route::post('/{farm}',[FarmController::class, 'update'])->name('update');
    Route::delete('/{farm}',[FarmController::class, 'destroy'])->name('destroy');

    // Toggles
    Route::patch('/{farm}/toggle-status',[FarmController::class, 'toggleStatus'])->name('toggle-status');
    Route::patch('/{farm}/toggle-featured',[FarmController::class, 'toggleFeatured'])->name('toggle-featured');
});
