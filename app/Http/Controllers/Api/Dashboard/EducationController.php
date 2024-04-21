<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\EducationRequest;
use App\Http\Resources\EducationResource;
use App\Models\Education;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    /**
     * Fetching education by user
     * @return AnonymousResourceCollection
     */
    public function fetchByUser(): AnonymousResourceCollection
    {
        $user = Auth::user();
        $education = $user->education()->get();

        return EducationResource::collection($education);

    }

    /**
     * Fetching education
     * @param Education $education
     * @return EducationResource
     */
    public function show(Education $education): EducationResource
    {
        return EducationResource::make($education);
    }

    /**
     * Create education data for user
     * @param EducationRequest $request
     * @return EducationResource
     */
    public function store(EducationRequest $request): EducationResource
    {
        $validated = $request->validated();
        $education = Education::query()->create($validated);

        return EducationResource::make($education);

    }

    /**
     * Update user's education data
     * @param Education $education
     * @param EducationRequest $request
     * @return EducationResource
     */
    public function update(Education $education, EducationRequest $request): EducationResource
    {
        $validated = $request->validated();
        $education->update($validated);

        return EducationResource::make($education->refresh());
    }
}
