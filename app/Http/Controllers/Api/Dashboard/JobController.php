<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Fetching job by user
     * @return AnonymousResourceCollection
     */
    public function fetchByUser(): AnonymousResourceCollection
    {
        $user = Auth::guard('api')->user();
        $job = $user->job()->get();

        return JobResource::collection($job);
    }

    /**
     * Show pro job by one
     * @param Job $job
     * @return JobResource
     */
    public function show(Job $job): JobResource
    {
        return JobResource::make($job);
    }

    /**
     * StoreRequest data in db
     * @param JobRequest $request
     * @return JobResource
     */
    public function store(JobRequest $request): JobResource
    {
        $validation = $request->validated();
        $job = Job::create($validation);

        return JobResource::make($job);
    }

    /**
     * Update data in db
     * @param Job $job
     * @param JobRequest $request
     * @return JobResource
     */
    public function update(Job $job, JobRequest $request): JobResource
    {
        $validation = $request->validated();
        $job->update($validation);

        return JobResource::make($job->refresh());
    }

    /**
     * Update experience value
     * @param Job $job
     * @param Request $request
     * @return JobResource
     */
    public function addExperience(Job $job, Request $request): JobResource
    {
        $job->update([
            'experience_count' => $request['experience_count']
        ]);

        return JobResource::make($job->refresh());
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
        return new JsonResponse('Job deleted successfully');
    }
}
