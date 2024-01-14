<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\ProJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Fetching job's experience
     * @param ProJob $proJob
     * @return JsonResponse
     */
    public function fetchByJob(ProJob $proJob): JsonResponse
    {
        // getting experience by job
        $experience = $proJob->experience()->get();

        // return response
        return new JsonResponse($experience);
    }

    /**
     * Show experience by one
     * @param Experience $experience
     * @return JsonResponse
     */
    public function show(Experience $experience): JsonResponse
    {
        //return response
        return new JsonResponse($experience);
    }

    /**
     * Store data in table
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //validated
        $validated = $request->validate([
            'job_id' => 'required|int|exists:pro_jobs,id',
            'place' => 'required|string|max:150',
            'post' => 'required|string|max:150',
            'started' => 'required|date',
            'expired' => 'nullable|date',
            'is_working' => 'required|boolean'
        ],
            [
                'place.required' => 'Поле "Место Работы " является обязательным для заполнения.',
                'post.required' => 'Поле "Специальность" является обязательным для заполнения.',
                'started.required' => 'Поле "Должность" является обязательным для заполнения.',
                'expired.required' => 'Поле "Период работы" является обязательным для заполнения.',
            ]);

        // store data
        $experience = Experience::query()->create($validated);

        // return response
        return new JsonResponse($experience);
    }

    /**
     * Update experience data in the table
     * @param Request $request
     * @param Experience $experience
     * @return JsonResponse
     */
    public function update(Experience $experience, Request $request): JsonResponse
    {
        //validated
        $validated = $request->validate([
            'place' => 'required|string|max:150',
            'post' => 'required|string|max:150',
            'started' => 'required|date',
            'expired' => 'nullable|date',
            'is_working' => 'required|boolean'
        ],
            [
                'place.required' => 'Поле "Место Работы " является обязательным для заполнения.',
                'post.required' => 'Поле "Специальность" является обязательным для заполнения.',
                'started.required' => 'Поле "Должность" является обязательным для заполнения.',
                'expired.required' => 'Поле "Период работы" является обязательным для заполнения.',
            ]);

        // store data
        $experience->update($validated);

        // return response
        return new JsonResponse($experience);
    }

}
