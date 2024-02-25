<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SocialOAuthController extends Controller
{
    public function redirect(): PromiseInterface|Response
    {
        return Http::withUrlParameters([
            'client_id' => env('MYID_ID'),
            'response_type' => 'code',
            'redirect_uri' => env('MYID_REDIRECT_URI'),
            'scope' => env('MYID_SCOPE'),
            'method' => 'strong',
            'state' => 'xyzABC123',
        ])->get('https://devmyid.uz/api/v1/oauth2/authorization');
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
