<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessRequest;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    /**
     * Getting user's pro businesses
     * @return AnonymousResourceCollection
     */
    public function fetchByUser(): AnonymousResourceCollection
    {
        $user = Auth::user();
        $businesses = $user->businesses()->get();

        return BusinessResource::collection($businesses);
    }

    /**
     * Show pro business
     * @param Business $business
     * @return BusinessResource
     */
    public function show(Business $business): BusinessResource
    {
        return BusinessResource::make($business);
    }


    /**
     * StoreRequest Business data
     * @param BusinessRequest $request
     * @return BusinessResource
     */
    public function store(BusinessRequest $request): BusinessResource
    {
        $validated = $request->validated();
        $proBusiness = Business::create($validated);

        return BusinessResource::make($proBusiness);
    }

    /**
     * Update Business data
     * @param Business $business
     * @param BusinessRequest $request
     * @return BusinessResource
     */
    public function update(Business $business, BusinessRequest $request): BusinessResource
    {
        $validated = $request->validated();
        $business->update($validated);

        return BusinessResource::make($business->refresh());
    }

    /**
     * @param Business $business
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImage(Business $business, Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        //delete old avatar if exists
        if ($business->image) {
            Storage::disk('public')->delete($business->image);
        }

        //store new avatar
        $avatarPath = $request->file('image')->store('business_image', 'public');

        //update user's avatar path in the database
        $business->update(['image' => $avatarPath]);

        //return response
        return new JsonResponse(['avatar_url' => asset("storage/{$avatarPath}")]);
    }
}
