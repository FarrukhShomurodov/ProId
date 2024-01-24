<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BanksData;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BanksDataController extends Controller
{
    /**
     * Getting user's banks data by business
     * @param Business $proBusiness
     * @return JsonResponse
     */
    public function fetchByBusiness(Business $proBusiness): JsonResponse
    {
        //banks data
        $banksData = $proBusiness->banks()->get();

        //return response
        return new JsonResponse($banksData);
    }

    /**
     * Show bank data
     * @param BanksData $banksData
     * @return JsonResponse
     */
    public function show(BanksData $banksData): JsonResponse
    {
        return new JsonResponse($banksData);
    }

    /**
     * Store bank data
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'name' => 'required|string',
            'name_of_banking_akkaunt' => 'required|string|max:100',
            'mfo' => 'required|integer',
            'payment_account' => 'required|integer',
        ],
        [
            'name.required' => 'Поле "Наиминование счета" является обязательным для заполнения.',
            'name_of_banking_akkaunt.required' => 'Поле "Расчетный счет" является обязательным для заполнения.',
            'mfo.required' => 'Поле "МФО" является обязательным для заполнения.',
            'payment_account.required' => 'Поле "Наиминование банка" является обязательным для заполнения.',
        ]);

        //store banking data in db
        $bankData = BanksData::query()->create($validated);

        //return response
        return new JsonResponse($bankData);
    }

    /**
     * Update banks data
     * @param BanksData $banksData
     * @param Request $request
     * @return JsonResponse
     */
    public function update(BanksData $banksData, Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'name' => 'required|string',
            'name_of_banking_akkaunt' => 'required|string|max:100',
            'mfo' => 'required|integer',
            'payment_account' => 'required|integer',
        ],
        [
            'name.required' => 'Поле "Наиминование счета" является обязательным для заполнения.',
            'name_of_banking_akkaunt.required' => 'Поле "Наиминование банка" является обязательным для заполнения.',
            'mfo.required' => 'Поле "МФО" является обязательным для заполнения.',
            'payment_account.required' => 'Поле "Расчетный счет" является обязательным для заполнения.',
        ]);

        //update
        $banksData->update($validated);

        //return response
        return new JsonResponse($banksData);
    }
}
