<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OTPController;
use App\Http\Controllers\Api\Auth\MyIdOauthController;
use App\Http\Controllers\Api\Dashboard\AddressController;
use App\Http\Controllers\Api\Dashboard\BanksDataController;
use App\Http\Controllers\Api\Dashboard\BoxOfficeController;
use App\Http\Controllers\Api\Dashboard\BusinessController;
use App\Http\Controllers\Api\Dashboard\EducationController;
use App\Http\Controllers\Api\Dashboard\ExperienceController;
use App\Http\Controllers\Api\Dashboard\JobController;
use App\Http\Controllers\Api\Dashboard\MFOController;
use App\Http\Controllers\Api\SendEmailController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('/has-user', [AuthController::class, 'hasUser']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// OTP
Route::post('/sendOTP', [OTPController::class, 'sendOTP']);
Route::post('/checkCode', [OTPController::class, 'checkCode']);

// OAuth with MyId
Route::get('/oauth/myid/redirect', [MyIdOauthController::class, 'redirect']);
Route::post('/oauth/myid/callback', [MyIdOauthController::class, 'callback']);

Route::middleware('auth:api')->group(function () {
    // Маршруты, доступные только аутентифицированным пользователям с действующим токеном
    Route::post('/logout', [AuthController::class, 'logout']);

    // User switch
    Route::post('/switch-user', [UserController::class, 'switchUser']);

    // User
    Route::get('/user', [UserController::class, 'getUser']);
    Route::get('/get-another-user/{id}', [UserController::class, 'show'])->whereNumber('id');
    Route::put('/update-user-data', [UserController::class, 'update']);
    Route::post('/edit-phone-number', [UserController::class, 'editPhoneNumber']);

    // Email
    Route::post('/send-verify-code-email', [SendEmailController::class, 'sendEmail']);
    Route::post('/send-check-verify-code-email', [SendEmailController::class, 'checkCode']);
    Route::post('/add-email', [UserController::class, 'addEmail']);
    Route::delete('/delete-email', [UserController::class, 'deleteEmail']);

    // User photo
    Route::post('/upload-avatar', [UserController::class, 'uploadAvatar']);
    Route::delete('/delete-avatar', [UserController::class, 'deleteAvatar']);

    // Addresses
    Route::get('/address', [AddressController::class, 'fetchByUser']);
    Route::apiResource('/address', AddressController::class)->except('index', 'destroy');

    // Business
    Route::get('/business', [BusinessController::class, 'fetchByUser']);
    Route::post('/business-image/{proBusiness}', [BusinessController::class, 'uploadImage'])->whereNumber('proBusiness');
    Route::apiResource('/business', BusinessController::class)->except('index', 'destroy');

    // Banks Data
    Route::get('/bank-data-by-business/{proBusiness}', [BanksDataController::class, 'fetchByBusiness'])->whereNumber('proBusiness');
    Route::apiResource('/bank-data', BanksDataController::class)->except('index', 'destroy')->parameter('bank-data', 'bankData');

    // MFO
    Route::get('bank-data-by-mfo', [MFOController::class, 'index']);

    // Box Offices
    Route::get('/box-office-disActivate/{boxOffice}', [BoxOfficeController::class, 'disActivate'])->whereNumber('boxOffice');
    Route::get('/box-office-activate/{boxOffice}', [BoxOfficeController::class, 'activate'])->whereNumber('boxOffice');
    Route::get('/box-office-by-business/{proBusiness}', [BoxOfficeController::class, 'fetchByBusiness'])->whereNumber('proBusiness');
    Route::apiResource('/box-office', BoxOfficeController::class)->parameter('box-office', 'boxOffice')->except('index');

    // Education
    Route::get('/education', [EducationController::class, 'fetchByUser']);
    Route::apiResource('/education', EducationController::class)->except('index', 'destroy');

    // Job
    Route::get('/job', [JobController::class, 'fetchByUser']);
    Route::put('/job-experience/{job}', [JobController::class, 'addExperience'])->whereNumber('job');
    Route::apiResource('/job', JobController::class)->except('index', 'destroy');

    // Experience
    Route::get('/experience-by-job/{proJob}', [ExperienceController::class, 'fetchByJob'])->whereNumber('proJob');
    Route::apiResource('/experience', ExperienceController::class)->except('index', 'destroy');
});
//
//
//Route::get('/redirect', function () {
//    $query = http_build_query([
//        'client_id' => '3',
//        'redirect_uri' => 'http://localhost:8000/api/callback',
//        'response_type' => 'code',
//        'scope' => '',
//        'state' => 'absdabsdabsdabsd',
//         'prompt' => 'consent', // "none", "consent", or "login"
//    ]);
//
//    return redirect('http://localhost:8000/oauth/authorize?' . $query);
//});
//
//
//Route::get('/callback', function (Request $request) {
//    $response = Http::asForm()->post('http://localhost:8000/oauth/token', [
//        'grant_type' => 'authorization_code',
//        'client_id' => '3',
//        'client_secret' => 'aG8nIvCbFG3M5Nh9DNFnNfbijQCUp7yHrMbC41iP',
//        'redirect_uri' => 'http://localhost:8000/api/callback',
//        'code' => $request->code,
//    ]);
//
//    return $response->json();
//});
