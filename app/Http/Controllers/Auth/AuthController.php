<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Lcobucci\JWT\Token\Parser;

class AuthController extends Controller
{
    /**
     * Checking the user for authorization
     * @param Request $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function login(Request $request): JsonResponse
    {

        //validate
        $validated = $request->validate([
            'phone_number' => 'required|regex:/^\+?[0-9]{10,}$/',
        ]);

        //getting user data by phone number
        $user = User::query()->where('phone_number', $validated['phone_number'])->first();


        //check has user
        if ($user) {
            //return response
            return new JsonResponse($user);
        }

        //return error
        throw new AuthenticationException("Нет аккаунта c таким номером.", [Response::HTTP_FORBIDDEN]);
    }

    /**
     * Getting access token when user confirm number
     * @param Request $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function token(Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'phone_number' => 'required|regex:/^\+?[0-9]{10,}$/',
        ]);

        //getting user by phone number
        $user = User::query()->where('phone_number', $validated['phone_number'])->first();
        if ($user) {
            Auth::login($user);

            $oauthData = Session::get('redirect_data');

            if (Session::has('redirect_data')) {
                $success['redirect_url'] = $oauthData;

            } else {
                $success['redirect_url'] = null;
            }

            $success['token'] = $user->createToken('token')->accessToken;
            $success['user'] = $user;

            $request->session()->regenerate();

            //return json with access token
            return new JsonResponse($success);
        }

        //error
        throw new AuthenticationException("Нет аккаунта c таким номером.", [Response::HTTP_FORBIDDEN]);
    }

    /**
     * Registration new user
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            //validate
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'surname' => 'required|string|max:70',
                'phone_number' => 'required|regex:/^\+?[0-9]{10,}$/',
            ]);

            //create user
            $user = User::query()->create($validated);

            //create access token
            $token = $user->createToken('token')->accessToken;

            $oauthData = Session::get('redirect_data');

            if (Session::has('redirect_data')) {
                $success['redirect_url'] = $oauthData;

            } else {
                $success['redirect_url'] = null;
            }

            $success['token'] = $token;
            $success['user'] = $user;

            $request->session()->regenerate();

            //return response
            return new JsonResponse($success, Response::HTTP_CREATED);
        } catch (Exception $e) {

            //return error
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $result = Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();

        if($result){
            $response = response()->json(['error'=>false,'message'=>'User logout successfully.','result'=>[]],200);
        }else{
            $response = response()->json(['error'=>true,'message'=>'Something is wrong.','result'=>[]],400);
        }

        return $response;
    }

}
