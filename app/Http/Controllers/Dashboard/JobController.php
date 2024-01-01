<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Fetching job by user
     * @param User $user
     * @return JsonResponse
     */
    public function fetchByUser(User $user): JsonResponse
    {
        // getting pro job by user
        $proJob = $user->proJob()->get();

        // return response
        return new JsonResponse($proJob);
    }

    /**
     * Show pro job by one
     * @param ProJob $proJob
     * @return JsonResponse
     */
    public function show(ProJob $proJob): JsonResponse
    {
        return new JsonResponse($proJob);
    }

    /**
     * Store data in db
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // validate
        $validation = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'profession' => 'required|string',
            'education_id' => ''
        ],
            [
                'type.required' => 'Поле "Тип професии" является обязательным для заполнения.',
                'profession.required' => 'Поле "Специальность" является обязательным для заполнения.',
                'education.required' => 'Поле "Информация обо образование" является обязательным для заполнения.'
            ]);

        // store data in db
        $proJob = ProJob::query()->create($validation);

        // return response
        return new JsonResponse($proJob);
    }

    /**
     * Update data in db
     * @param ProJob $proJob
     * @param Request $request
     * @return JsonResponse
     */
    public function update(ProJob $proJob, Request $request): JsonResponse
    {
        // validate
        $validation = $request->validate([
            'type' => 'required|string',
            'profession' => 'required|string',
            'education_id' => ''
        ],
            [
                'type.required' => 'Поле "Тип професии" является обязательным для заполнения.',
                'profession.required' => 'Поле "Специальность" является обязательным для заполнения.',
                'education.required' => 'Поле "Информация обо образование" является обязательным для заполнения.'
            ]);

        // store data in db
        $proJob->update($validation);

        // return response
        return new JsonResponse($proJob);
    }

    /**
     * Update experience value
     * @param ProJob $proJob
     * @param Request $request
     * @return JsonResponse
     */
    public function addExperience(ProJob $proJob, Request $request): JsonResponse
    {
        //validated
        $validated = $request->validate([
            'experience_count' => 'required|int'
        ]);

        // update experience count
        $proJob->update([
            'experience_count' => $validated['experience_count']
        ]);

        // return response
        return new JsonResponse($proJob);
    }

    /**
     * Delete data from db
     * @param ProJob $proJob
     * @return JsonResponse
     */
    public function destroy(ProJob $proJob): JsonResponse
    {
        $proJob->delete();

        // return response
        return new JsonResponse($proJob);
    }
}
