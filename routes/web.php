<?php

use App\Http\Controllers\AcademySiteController;
use Illuminate\Support\Facades\Route;

Route::domain('{accountSubdomain}.'.config('almanasa.root_domain'))
    ->get('/{page?}', AcademySiteController::class)
    ->where('page', '.*');

Route::get('/', function () {
    return view('welcome');
});
