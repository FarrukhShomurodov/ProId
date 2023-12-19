<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Update user data
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'surname' => 'required|string|max:70',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
        ]);

        //update user information
        $user->update($validated);

        //return response
        return new JsonResponse($user);
    }


    /**
     * Edit phone number
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function editPhoneNumber(Request $request, User $user): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'phone_number' => 'required|regex:/^\+?[0-9]{10,}$/',
        ]);

        //update user phone number
        $user->update($validated);

        //return response
        return new JsonResponse($user);
    }

    /**
     * Add User Email
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function addEmail(Request $request, User $user): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        //add user email in user data
        $user->update($validated);

        //return response
        return new JsonResponse($user);
    }

    /**
     * Delete user email from db
     * @param User $user
     * @return JsonResponse
     */
    public function deleteEmail(User $user): JsonResponse
    {
        //set null in email
        $user->update([
            'email' => null
        ]);

        //return response
        return new JsonResponse('email delete successfully');
    }

    /**
     * Upload user photo
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function uploadAvatar(Request $request, User $user): JsonResponse
    {
        //validate
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        //delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        //store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');

        //update user's avatar path in the database
        $user->update(['avatar' => $avatarPath]);

        //return response
        return new JsonResponse(['avatar_url' => asset("storage/{$avatarPath}")]);
    }

    /**
     * Delete user photo
     * @param User $user
     * @return JsonResponse
     */
    public function deleteAvatar(User $user): JsonResponse
    {
        //delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        //update user's avatar path in the database
        $user->update(['avatar' => null]);

        //return response
        return new JsonResponse(['avatar_url' => 'deleted successfully']);
    }

}
