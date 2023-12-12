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

// Route::get('redirect', function (Request $request) {
//     $request->session()->put('state', $state = Str::random(40));

//     $query = http_build_query([
//         'client_id' => 3,
//         'redirect_uri' => 'https://id.in-pro.net/api/oauth/callback',
//         'response_type' => 'code',
//         'scope' => '',
//         'prompt' => 'consent',
//         'state' => $state,
//     ]);

//     return redirect('https://id.in-pro.net/oauth/authorize?'.$query);
// });



// Route::get('oauth/callback', function (Request $request) {
//     $state = $request->session()->pull('state');

//     throw_unless(
//         strlen($state) > 0 && $state === $request->state,
//         InvalidArgumentException::class,
//         'Invalid state value.'
//     );

//     $response = Http::asForm()->post('https://id.in-pro.net/oauth/token', [
//         'grant_type' => 'authorization_code',
//         'client_id' => 3,
//         'client_secret' => 'aoqf9l9Ovl35PzVhBgq6qYZ7cvK0wFHxH0GWkyYi',
//         'redirect_uri' => 'https://id.in-pro.net/api/oauth/callback',
//         'code' => $request->code,
//     ]);

//     return $response->json();
// });

//Auth
Route::post('/login', [AuthController::class, 'login']);
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
