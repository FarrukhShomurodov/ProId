<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Fetching job by user
     * @return JsonResponse
     */
    public function fetchByUser(): JsonResponse
    {
        // chech user auth
        $user = Auth::user();

        // getting pro job by user
        $proJob = $user->job()->get();

        // return response
        return new JsonResponse($proJob);
    }

    /**
     * Show pro job by one
     * @param Job $proJob
     * @return JsonResponse
     */
    public function show(Job $proJob): JsonResponse
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
        // set auth user id
        $request['user_id'] = Auth::user()->id;

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
        $proJob = Job::query()->create($validation);

        // return response
        return new JsonResponse($proJob);
    }

    /**
     * Update data in db
     * @param Job $proJob
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Job $proJob, Request $request): JsonResponse
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
     * @param Job $proJob
     * @param Request $request
     * @return JsonResponse
     */
    public function addExperience(Job $proJob, Request $request): JsonResponse
    {
        // update experience count
        $proJob->update([
            'experience_count' => $request['experience_count']
        ]);

        // return response
        return new JsonResponse($proJob);
    }

    /**
     * Delete data from db
     * @param Job $proJob
     * @return JsonResponse
     */
    public function destroy(Job $proJob): JsonResponse
    {
        $proJob->delete();

        // return response
        return new JsonResponse($proJob);
    }
}
