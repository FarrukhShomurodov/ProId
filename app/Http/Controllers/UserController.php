<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Update user data
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'surname' => 'required|string|max:70',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
        ]);

        //update user information
        $this->user->update($validated);

        //return response
        return new JsonResponse($this->user);
    }

    /**
     * Edit phone number
     * @param Request $request
     * @return JsonResponse
     */
    public function editPhoneNumber(Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'phone_number' => 'required|regex:/^\+?[0-9]{10,}$/',
        ]);

        //update user phone number
        $this->user->update($validated);

        //return response
        return new JsonResponse($this->user);
    }

    /**
     * Add User Email
     * @param Request $request
     * @return JsonResponse
     */
    public function addEmail(Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        //add user email in user data
        $this->user->update($validated);

        //return response
        return new JsonResponse($this->user);
    }

    /**
     * Delete user email from db
     * @return JsonResponse
     */
    public function deleteEmail(): JsonResponse
    {
        //set null in email
        $this->user->update([
            'email' => null
        ]);

        //return response
        return new JsonResponse('email delete successfully');
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