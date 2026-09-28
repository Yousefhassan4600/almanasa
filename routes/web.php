<?php

use App\Http\Controllers\AcademySiteController;
use App\Http\Controllers\Admin\BunnyStreamTusUploadController;
use App\Http\Controllers\Admin\BunnyStreamVideoController;
use App\Http\Controllers\ProviderWebsiteAuthController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::post('/admin/bunny-stream/tus-upload', BunnyStreamTusUploadController::class)
    ->middleware(['auth', 'current.account:dashboard'])
    ->name('admin.bunny-stream.tus-upload');

Route::delete('/admin/bunny-stream/videos', [BunnyStreamVideoController::class, 'destroy'])
    ->middleware(['auth', 'current.account:dashboard'])
    ->name('admin.bunny-stream.videos.destroy');

Route::domain('{accountSubdomain}.'.config('almanasa.root_domain'))->group(function (): void {
    Route::prefix('{locale}')->where(['locale' => 'ar|en'])->middleware('website.locale')->group(function (): void {
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

    Route::get('/{page?}', function (Request $request, string $accountSubdomain, ?string $page = null): RedirectResponse {
        $page = trim($page ?? '', '/');
        $page = Str::endsWith($page, '.html') ? Str::beforeLast($page, '.html') : $page;
        $page = $page === 'index' ? '' : $page;
        $path = '/ar'.($page === '' ? '' : '/'.$page);

        return redirect($path.($request->getQueryString() ? '?'.$request->getQueryString() : ''), 301);
    })->where('page', '.*');
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
