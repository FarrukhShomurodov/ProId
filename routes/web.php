<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->where('any', '[\/\w\.-]*');
Route::post('/login', [AuthController::class, 'login'])->name('login');
