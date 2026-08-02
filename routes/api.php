<?php

use App\Http\Controllers\Api\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '{locale}',
    'where'  => ['locale' => 'ar|en|ur'],
    'middleware' => ['api', 'set.locale'],
], function () {
Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::group([
            'prefix' => 'student',
        ], function () {
            Route::post('login', [LoginController::class, 'login']);
            Route::post('verify', [LoginController::class, 'verify']);

              Route::middleware('role.token:student')->group(function () {
                // Route::get('me', [LoginController::class, 'me']);
                // Route::post('logout', [LoginController::class, 'customerLogout']);
                // Route::post('edit-profile', [LoginController::class, 'editProfile']);

                Route::get('student-services', [LoginController::class, 'studentServices']);
            });

        });

    });


});
