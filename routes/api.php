<?php

use App\Http\Controllers\Api\Academy\StudentLearning;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\General\GeneralController;
use App\Http\Controllers\Api\StudentLearning\StudentLearningController;
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
            Route::post('login', [AuthController::class, 'login']);
            Route::post('verify', [AuthController::class, 'verify']);

              Route::middleware('role.token:student')->group(function () {
                 Route::get('me', [AuthController::class, 'me']);
                 Route::post('logout', [AuthController::class, 'logout']);
                 Route::post('edit-profile', [AuthController::class, 'editProfile']);
                 Route::get('student-services', [AuthController::class, 'studentServices']);
            });

        });

    });

       Route::group([
        'prefix' => 'general',
    ], function () {
        Route::get('countries', [GeneralController::class, 'getCountries']);
        Route::get('cities', [GeneralController::class, 'getCities']);
        Route::get('educational-stages', [GeneralController::class, 'getEducationalStages']);
        Route::get('grades', [GeneralController::class, 'getGrades']);
    });

    Route::group([
        'prefix'     => 'studentlearning',
        'middleware' => ['role.token:student'],
    ], function () {
        Route::get('my-subscribed-subjects', [StudentLearningController::class, 'getMySubscribedSubjects']);
        Route::get('single-teacher-page', [StudentLearningController::class, 'getSingleTeacherPage']);
        Route::get('lesson-item', [StudentLearningController::class, 'getLessonItem']);

    });

});
