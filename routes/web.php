<?php

use App\Http\Controllers\Web\PrivacyPolicyPageController;
use App\Http\Controllers\Web\SupportPageController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/admin_auth.php';

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/support-page', [SupportPageController::class, 'show'])->name('public.support.page');
Route::get('/privacy-policy-page', [PrivacyPolicyPageController::class, 'show'])->name('public.privacy.policy');
