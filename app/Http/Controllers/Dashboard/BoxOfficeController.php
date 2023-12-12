<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BoxOffice;
use App\Models\ProBusiness;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoxOfficeController extends Controller
{
    /**
     * Show box offices
     * @param BoxOffice $boxOffice
     * @return JsonResponse
     */
    public function show(BoxOffice $boxOffice)
    {
        return new JsonResponse($boxOffice);
    }

    /**
     * Fetching box offices by Business
     * @param ProBusiness $proBusiness
     * @return JsonResponse
     */
    public function fetchByBusiness(ProBusiness $proBusiness): JsonResponse
    {
        //getting box offices by business
        $boxOffice = $proBusiness->boxOffices()->get();

        //return response
        return new JsonResponse($boxOffice);
    }

    /**
     * Store box office data in db
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'business_id' => 'required|integer|exists:pro_businesses,id',
            'name' => 'required|string|max:100',
            'service' => 'required|string',
            'bank_data_id' => 'required|integer|exists:banks_data,id',
        ]);

        //store box office data in db
        $boxOffice = BoxOffice::query()->create($validated);

        //return response
        return new JsonResponse($boxOffice);
    }

    /**
     * Store box office data in db
     * @param Request $request
     * @return JsonResponse
     */
    public function update(BoxOffice $boxOffice, Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'service' => 'required|string',
            'bank_data_id' => 'required|integer|exists:banks_data,id',
        ]);

        //update box office data in db
        $boxOffice->update($validated);

        //return response
        return new JsonResponse($boxOffice);
    }

    /**
     * Dis Activate selected BoxOffice
     * @param BoxOffice $boxOffice
     * @return JsonResponse
     */
    public function disActivate(BoxOffice $boxOffice): JsonResponse
    {
        $boxOffice->update([
            'isActive' => 0
        ]);

        //return response
        return new JsonResponse($boxOffice);
    }

     /**
     *  Activate selected BoxOffice
     * @param BoxOffice $boxOffice
     * @return JsonResponse
     */
    public function activate(BoxOffice $boxOffice): JsonResponse
    {
        $boxOffice->update([
            'isActive' => 1
        ]);

        //return response
        return new JsonResponse($boxOffice);
    }

    /**
     * Delete Box Office from db
     * @param BoxOffice $boxOffice
     * @return JsonResponse
     */
    public function destroy(BoxOffice $boxOffice)
    {
        //delete box office from db
        $boxOffice->delete();

        //return response
        return new JsonResponse('box office successfuly deleted');
    }

}
