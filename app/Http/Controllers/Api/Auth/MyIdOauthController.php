<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MyIdOauthController extends Controller
{
    /**
     * @return Application|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
     */
    public function redirect(): Application|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $state = Str::random(40);

        $headers = [
            'Content-Type' => 'PARAMS',
            'Accept' => 'application/json'
        ];

        $query = http_build_query([
            'client_id' => env('MYID_ID'),
            'response_type' => 'code',
            'redirect_uri' => env('MYID_REDIRECT_URI'),
            'scope' => env('MYID_SCOPE'),
            'method' => 'strong',
            'state' => $state,
        ]);

        return redirect('https://devmyid.uz/api/v1/oauth2/authorization?' . $query)->withHeaders($headers);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function callback(Request $request): JsonResponse
    {
        try {
            // Get authorization code from the request
            $code = $request->get('code');

            // Exchange authorization code for an access token
            $response = Http::post('https://devmyid.uz/api/v1/oauth2/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => env('MYID_ID'),
                'client_secret' => env('MYID_SECRET'),
            ]);

            // Check if the token request was successful
            if ($response->successful()) {
                $accessToken = $response['access_token'];

                // Fetch user information using the access token
                $userDataResponse = Http::withToken($accessToken)->get('https://devmyid.uz/api/v1/users/me');

                // Check if user data retrieval was successful
                if ($userDataResponse->successful()) {
                    $userData = $userDataResponse->json();
                    $userData = $userData['profile']["common_data"];

                    $user = Auth::user();
                    $user->name = $userData['first_name'];
                    $user->surname = $userData['last_name'];
                    $user->date_of_birth = $userData['birth_date'];
                    $user->gender = $userData['gender'];
                    $user->save();

                    // Return a response indicating success
                    return response()->json(['message' => 'User data updated successfully', 'data' => $user]);
                } else {
                    // Handle failure to retrieve user data
                    return response()->json(['error' => 'Failed to retrieve user data'], $userDataResponse->status());
                }
            } else {
                // Handle failure to obtain access token
                return response()->json(['error' => 'Failed to obtain access token'], $response->status());
            }
        } catch (Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}
