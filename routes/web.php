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
    Route::get('/my_lessons', [AcademySiteController::class, '__invoke'])
        ->defaults('page', 'my_lessons')
        ->middleware(['auth', 'current.account:website'])
        ->name('provider.website.my-lessons');
    Route::post('/logout', [ProviderWebsiteAuthController::class, 'logout'])
        ->middleware('auth')
        ->name('provider.website.logout');

    Route::get('/{page?}', AcademySiteController::class)
        ->where('page', '.*');
});

Route::get('/', function () {
    $path = public_path('landing.html');

    if (! is_file($path)) {
        return view('welcome');
    }

    $html = file_get_contents($path);

    abort_if($html === false, 404);

    $html = str_replace(
        [
            '<title>Edu Learning</title>',
            'src="tsconfig.js"',
        ],
        [
            '<title>'.e(config('app.name')).'</title>',
            'src="/academy/assets/js/ts_congig.js"',
        ],
        $html,
    );

    return response($html, 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
});
