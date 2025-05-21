<?php

use App\Http\Controllers\Web\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StudentController::class, 'index']);
Route::resource('students', StudentController::class);
