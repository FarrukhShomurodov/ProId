<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    /**
     * Show pro business
     * @param Business $proBusiness
     * @return JsonResponse
     */
    public function show(Business $proBusiness): JsonResponse
    {
        return new JsonResponse($proBusiness);
    }

    /**
     * Getting user's pro businesses
     * @param User $user
     * @return JsonResponse
     */
    public function fetchByUser(User $user): JsonResponse
    {
        //businesses
        $businesses = $user->businesses()->get();

        //return response
        return new JsonResponse($businesses);
    }

    /**
     * Store Business data
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'inn' => 'required|integer',
            'name_of_business' => 'required|string',
            'form_of_business' => 'required|string',
            'oked' => 'required|integer',
            'address' => 'required|string',
        ],
        [
            'inn.required' => 'Поле "ИНН" является обязательным для заполнения.',
            'name_of_business.required' => 'Поле "Название фирмы" является обязательным для заполнения.',
            'form_of_business.required' => 'Поле "Форма бизнеса " является обязательным для заполнения.',
            'oked.required' => 'Поле "ОКЭД бизнеса" является обязательным для заполнения.',
            'address.required' => 'Поле "Юридический адрес" является обязательным для заполнения.',
        ]);

        //store proBusiness data in db
        $proBusiness = Business::query()->create($validated);

        //return response
        return new JsonResponse($proBusiness);
    }

    /**
     * Update Business data
     * @param Business $proBusiness
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Business $proBusiness, Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'inn' => 'required|integer',
            'name_of_business' => 'required|string',
            'form_of_business' => 'required|string',
            'oked' => 'required|integer',
            'address' => 'required|string',
        ],
        [
            'inn.required' => 'Поле "ИНН" является обязательным для заполнения.',
            'name_of_business.required' => 'Поле "Название фирмы" является обязательным для заполнения.',
            'form_of_business.required' => 'Поле "Форма бизнеса " является обязательным для заполнения.',
            'oked.required' => 'Поле "ОКЭД бизнеса" является обязательным для заполнения.',
            'address.required' => 'Поле "Юридический адрес" является обязательным для заполнения.',
        ]);

        //update proBusinesses in db
        $proBusiness->update($validated);

        //return response
        return new JsonResponse($proBusiness);
    }

    /**
     * @param Business $proBusiness
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImage(Business $proBusiness, Request $request): JsonResponse
    {
        //validate
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        //delete old avatar if exists
        if ($proBusiness->image) {
            Storage::disk('public')->delete($proBusiness->image);
        }

        //store new avatar
        $avatarPath = $request->file('image')->store('business_image', 'public');

        //update user's avatar path in the database
        $proBusiness->update(['image' => $avatarPath]);

        //return response
        return new JsonResponse(['avatar_url' => asset("storage/{$avatarPath}")]);
    }
}
