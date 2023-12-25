<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Fetching education by user
     * @param User $user
     * @return JsonResponse
     */
    public function fetchByUser(User $user): JsonResponse
    {
        // getting education data by user
        $education = $user->education()->get();

        //return response
        return new JsonResponse($education);
    }

    /**
     * Fetching education
     * @param Education $education
     * @return JsonResponse
     */
    public function show(Education $education): JsonResponse
    {
        //return response
        return new JsonResponse($education);
    }

    /**
     * Create education data for user
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // validate
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'name' => 'required|string',
            'is_studying' => 'required|boolean',
            'started' => 'required|date',
            'expired' => 'required|date',
        ],
            [
                'type.required' => 'Поле "Вид образования" является обязательным для заполнения.',
                'name.required' => 'Поле "Наименование учебного заведения" является обязательным для заполнения.',
                'is_studying.required' => 'Поле "МФО" является обязательным для заполнения.',
                'started.required' => 'Поле "Период обучения" является обязательным для заполнения.',
                'expired.required' => 'Поле "Период обучения" является обязательным для заполнения.',
            ]);

        // store education data in db
        $education = Education::query()->create($validated);

        // return response
        return new JsonResponse($education);
    }

    /**
     * Update user's education data
     * @param Education $education
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Education $education, Request $request): JsonResponse
    {
        // validate
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'is_studying' => 'required|boolean',
            'started' => 'required|date',
            'expired' => 'required|date',
        ],
            [
                'type.required' => 'Поле "Вид образования" является обязательным для заполнения.',
                'name.required' => 'Поле "Наименование учебного заведения" является обязательным для заполнения.',
                'is_studying.required' => 'Поле "МФО" является обязательным для заполнения.',
                'started.required' => 'Поле "Период обучения" является обязательным для заполнения.',
                'expired.required' => 'Поле "Период обучения" является обязательным для заполнения.',
            ]);

        // store education data in db
        $education->update($validated);

        // return response
        return new JsonResponse($education);
    }

    /**
     * Delete user's education data
     * @param Education $education
     * @return JsonResponse
     */
    public function destroy(Education $education): JsonResponse
    {
        $education->delete();

        // return response
        return new JsonResponse('deleted successfully');
    }
}
