<?php

use App\Http\Controllers\AcademySiteController;
use App\Http\Controllers\ProviderWebsiteAuthController;
use Illuminate\Support\Facades\Route;

Route::domain('{accountSubdomain}.'.config('almanasa.root_domain'))->group(function (): void {
    Route::get('/login', [AcademySiteController::class, '__invoke'])
        ->defaults('page', 'login')
        ->name('provider.website.login');
    Route::get('/register', [AcademySiteController::class, '__invoke'])
        ->defaults('page', 'register')
        ->name('provider.website.register');
    Route::get('/profile', [AcademySiteController::class, '__invoke'])
        ->defaults('page', 'profile')
        ->middleware(['auth', 'current.account:website'])
        ->name('provider.website.profile');
    Route::post('/logout', [ProviderWebsiteAuthController::class, 'logout'])
        ->middleware('auth')
        ->name('provider.website.logout');

    Route::get('/{page?}', AcademySiteController::class)
        ->where('page', '.*');
});

Route::get('/', function () {
    return view('welcome');
});
