<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EmailRequest;
use App\Http\Requests\User\PhoneNumberRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function getUser(): UserResource
    {
        return UserResource::make($this->user);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function switchUser(Request $request): JsonResponse
    {
        try {
            $switchedUser = User::find($request->user_id);

            if (!$switchedUser) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            if ($request->user())
                $request->user()->token()->revoke();

            $accessToken = $switchedUser->createToken('token')->accessToken;

            return new JsonResponse(['access_token' => $accessToken, 'user' => UserResource::make($switchedUser)], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $id
     * @return UserResource|JsonResponse
     */
    public function show($id): UserResource|JsonResponse
    {
        $user = User::query()->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return UserResource::make($user);
    }

    /**
     * Update user data
     * @param UpdateRequest $request
     * @return UserResource
     */
    public function update(UpdateRequest $request): UserResource
    {
        $validated = $request->validated();
        $this->user->update($validated);

        return UserResource::make($this->user->refresh());
    }

    /**
     * Edit phone number
     * @param PhoneNumberRequest $request
     * @return UserResource
     */
    public function editPhoneNumber(PhoneNumberRequest $request): UserResource
    {
        $validated = $request->validated();
        $this->user->update($validated);

        return UserResource::make($this->user->refresh());
    }

    /**
     * Add User Email
     * @param EmailRequest $request
     * @return UserResource
     */
    public function addEmail(EmailRequest $request): UserResource
    {
        $validated = $request->validated();
        $this->user->update($validated);

        return UserResource::make($this->user->refresh());
    }

    /**
     * Delete user email from db
     * @return UserResource
     */
    public function deleteEmail(): UserResource
    {
        $this->user->update([
            'email' => null
        ]);

        return UserResource::make($this->user->refresh());
    }

    /**
     * Upload user photo
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        //validate
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        //delete old avatar if exists
        if ($this->user->avatar) {
            Storage::disk('public')->delete($this->user->avatar);
        }

        //store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');

        //update user's avatar path in the database
        $this->user->update(['avatar' => $avatarPath]);

        //return response
        return new JsonResponse(['avatar_url' => asset("storage/{$avatarPath}")]);
    }

    /**
     * Delete user photo
     * @return JsonResponse
     */
    public function deleteAvatar(): JsonResponse
    {
        //delete old avatar if exists
        if ($this->user->avatar) {
            Storage::disk('public')->delete($this->user->avatar);
        }

        //update user's avatar path in the database
        $this->user->update(['avatar' => null]);

        //return response
        return new JsonResponse(['avatar_url' => 'deleted successfully']);
    }
}
