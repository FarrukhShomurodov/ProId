<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\BoxOffice\StoreRequest;
use App\Http\Requests\BoxOffice\UpdateRequest;
use App\Http\Resources\BoxOfficeResource;
use App\Models\BoxOffice;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BoxOfficeController extends Controller
{

    /**
     * Fetching box offices by Business
     * @param Business $proBusiness
     * @return AnonymousResourceCollection
     */
    public function fetchByBusiness(Business $proBusiness): AnonymousResourceCollection
    {
        $boxOffice = $proBusiness->boxOffices()->get();

        return BoxOfficeResource::collection($boxOffice);
    }

    /**
     * Show box offices
     * @param BoxOffice $boxOffice
     * @return BoxOfficeResource
     */
    public function show(BoxOffice $boxOffice): BoxOfficeResource
    {
        return BoxOfficeResource::make($boxOffice);
    }

    /**
     * StoreRequest box office data in db
     * @param StoreRequest $request
     * @return BoxOfficeResource
     */
    public function store(StoreRequest $request): BoxOfficeResource
    {
        $validated = $request->validated();
        $boxOffice = BoxOffice::create($validated);

        return BoxOfficeResource::make($boxOffice->fresh());
    }

    /**
     * StoreRequest box office data in db
     * @param BoxOffice $boxOffice
     * @param UpdateRequest $request
     * @return BoxOfficeResource
     */
    public function update(BoxOffice $boxOffice, UpdateRequest $request): BoxOfficeResource
    {
        $validated = $request->validated();
        $boxOffice->update($validated);

        return BoxOfficeResource::make($boxOffice->refresh());
    }

    /**
     * DisActivate selected BoxOffice
     * @param BoxOffice $boxOffice
     * @return BoxOfficeResource
     */
    public function disActivate(BoxOffice $boxOffice): BoxOfficeResource
    {
        $boxOffice->update([
            'isActive' => 0
        ]);

        return BoxOfficeResource::make($boxOffice->refresh());
    }

    /**
     *  Activate selected BoxOffice
     * @param BoxOffice $boxOffice
     * @return BoxOfficeResource
     */
    public function activate(BoxOffice $boxOffice): BoxOfficeResource
    {
        $boxOffice->update([
            'isActive' => 1
        ]);

        return BoxOfficeResource::make($boxOffice->refresh());
    }

    /**
     * Delete Box Office from db
     * @param BoxOffice $boxOffice
     * @return JsonResponse
     */
    public function destroy(BoxOffice $boxOffice): JsonResponse
    {
        $boxOffice->delete();

        return new JsonResponse('box office successfully deleted');
    }

}
