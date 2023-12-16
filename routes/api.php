<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\Dashboard\AddressController;
use App\Http\Controllers\Dashboard\BanksDataController;
use App\Http\Controllers\Dashboard\BoxOfficeController;
use App\Http\Controllers\Dashboard\MFOController;
use App\Http\Controllers\Dashboard\ProBusinessController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Auth
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class,'register']);
Route::post('/token',[AuthController::class, 'token'])->name('token');
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);


Route::middleware('auth:api')->group(function () {
    // Маршруты, доступные только аутентифицированным пользователям с действующим токеном

    // User
    Route::put('/update/{user}', [UserController::class,'update']);
    Route::post('/edit-phone-number/{user}', [UserController::class,'editPhoneNumber']);
    Route::post('/add-email/{user}', [UserController::class,'addEmail']);

    // User photo
    Route::post('/upload-avatar/{user}', [UserController::class,'uploadAvatar']);
    Route::delete('/delete-avatar/{user}', [UserController::class,'deleteAvatar']);

    // Addresses
    Route::get('/address/{user}', [AddressController::class, 'fetchByUser']);
    Route::get('/address-show/{address}', [AddressController::class, 'show']);
    Route::post('/address', [AddressController::class, 'store']);
    Route::put('/address/{address}', [AddressController::class, 'update']);

    // Business
    Route::get('/fetch-by-user/{user}', [ProBusinessController::class, 'fetchByUser']);
    Route::get('/pro-business-show/{proBusiness}',[ProBusinessController::class, 'show']);
    Route::post('/pro-business', [ProBusinessController::class, 'store']);
    Route::post('/pro-business-image/{proBusiness}', [ProBusinessController::class, 'uploadImage']);
    Route::put('/pro-business/{proBusiness}', [ProBusinessController::class, 'update']);

    // Banks Data
    Route::get('/banking-data-show/{banksData}', [BanksDataController::class, 'show']);
    Route::get('/banking-data-fetch/{proBusiness}',[BanksDataController::class, 'fetchByBusiness']);
    Route::post('/banks-data',[BanksDataController::class, 'store']);
    Route::put('/banks-data/{banksData}',[BanksDataController::class, 'update']);

    // MFO
    Route::get('bank-data-by-mfo', [MFOController::class, 'index']);

    // Box Offices
    Route::get('/box-offices/{proBusiness}', [BoxOfficeController::class, 'fetchByBusiness']);
    Route::get('/box-offices-show/{boxOffice}', [BoxOfficeController::class, 'show']);
    Route::get('/box-offices-disActivate/{boxOffice}', [BoxOfficeController::class, 'disActivate']);
    Route::get('/box-offices-activate/{boxOffice}', [BoxOfficeController::class, 'activate']);
    Route::put('/box-offices/{boxOffice}', [BoxOfficeController::class, 'update']);
    Route::post('/box-offices/', [BoxOfficeController::class, 'store']);
    Route::delete('/box-offices/{boxOffice}', [BoxOfficeController::class, 'destroy']);
});

// OTP
Route::post('/sendOTP', [OTPController::class,'sendOTP']);
Route::post('/checkCode', [OTPController::class,'checkCode']);
Route::delete('/deleteCode', [OTPController::class,'deleteCode']);
