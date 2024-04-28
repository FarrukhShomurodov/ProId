<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PhoneNumberRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Checking the user for authorization
     * @param PhoneNumberRequest $request
     * @return JsonResponse
     */
    public function hasUser(PhoneNumberRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::query()->where('phone_number', $validated['phone_number'])->first();

        if ($user != null) {
            return new JsonResponse(UserResource::make($user), Response::HTTP_OK);
        } else {
            return new JsonResponse('Нет аккаунта c таким номером', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Getting access token when user confirm number
     * @param PhoneNumberRequest $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function login(PhoneNumberRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::query()->where('phone_number', $validated['phone_number'])->first();

            $oauthData = Session::get('redirect_data');

            // set oauth redirect url if it has
            $redirectUrl = null;
            if (Session::has('redirect_data')) $redirectUrl = $oauthData;
            Session::forget('redirect_data');


            if ($user) {
                Auth::guard('web')->login($user);
                $accessToken = $user->createToken('token')->accessToken;
            }

            $request->session()->regenerate();

            return new JsonResponse([
                'access_token' => $accessToken,
                'redirect_url' => $redirectUrl,
                'user' => UserResource::make($user),
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Registration new user
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function register(StoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::query()->create($validated);

            Auth::guard('web')->login($user);
            $accessToken = $user->createToken('token')->accessToken;

            $oauthData = Session::get('redirect_data');

            $redirectUrl = null;
            if (Session::has('redirect_data')) $redirectUrl = $oauthData;
            Session::forget('redirect_data');


            $request->session()->regenerate();

            return new JsonResponse([
                'access_token' => $accessToken,
                'redirect_url' => $redirectUrl,
                'user' => UserResource::make($user)
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {

            if ($request->user()->token()) {
                $request->user()->token()->revoke();
            }

            Auth::guard('web')->logout();

            return response()->json(['message' => 'Logged out successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to logout'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
