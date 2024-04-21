<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\StoreRequest;
use App\Http\Requests\Experience\UpdateRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use App\Models\Job;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExperienceController extends Controller
{
    /**
     * Fetching job's experience
     * @param Job $proJob
     * @return AnonymousResourceCollection
     */
    public function fetchByJob(Job $proJob): AnonymousResourceCollection
    {
        $experience = $proJob->experience()->get();
        return ExperienceResource::collection($experience);
    }

    /**
     * Show experience by one
     * @param Experience $experience
     * @return ExperienceResource
     */
    public function show(Experience $experience): ExperienceResource
    {
        return ExperienceResource::make($experience);
    }

    /**
     * StoreRequest data in table
     * @param StoreRequest $request
     * @return ExperienceResource
     */
    public function store(StoreRequest $request): ExperienceResource
    {
        $validated = $request->validated();
        $experience = Experience::create($validated);

        return ExperienceResource::make($experience);
    }

    /**
     * Update experience data in the table
     * @param Experience $experience
     * @param UpdateRequest $request
     * @return ExperienceResource
     */
    public function update(Experience $experience, UpdateRequest $request): ExperienceResource
    {
        $validated = $request->validated();
        $experience->update($validated);

        return ExperienceResource::make($experience->refresh());
    }

}
