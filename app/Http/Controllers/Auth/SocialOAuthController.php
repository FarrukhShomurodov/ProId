<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialOAuthController extends Controller
{
    /**
     * @return RedirectResponse|\Illuminate\Http\RedirectResponse
     */
    public function redirect(): RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        return Socialite::driver('myid')->redirect();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function callback(Request $request): Response
    {
        // getting code form code
        $code = $request->get('code');

        // getting access token
        $response = Http::post('https://devmyid.uz/api/v1/oauth2/authorization', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => env('MYID_ID'),
            'client_secret' => env('MYID_SECRET'),
        ]);

        // user
        $callbackUser = $this->getUser($response['access_token']);

        // update user with passport data
        $user = User::query()->update([$callbackUser]);

        // return response
        return $user;
    }


    /**
     * @param $accessToken
     * @return PromiseInterface|Response
     */
    public function getUser($accessToken): PromiseInterface|Response
    {
        return Http::withToken($accessToken,'Bearer')->get('https://devmyid.uz/api/v1/users/me');
    }
}
