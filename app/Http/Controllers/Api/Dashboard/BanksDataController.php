<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankData\StoreRequest;
use App\Http\Requests\BankData\UpdateRequest;
use App\Http\Resources\BankDataResource;
use App\Models\BanksData;
use App\Models\Business;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BanksDataController extends Controller
{
    /**
     * Getting user's banks data by business
     * @param Business $proBusiness
     * @return AnonymousResourceCollection
     */
    public function fetchByBusiness(Business $proBusiness): AnonymousResourceCollection
    {
        $banksData = $proBusiness->banks()->get();

        return BankDataResource::collection($banksData);
    }

    /**
     * Show bank data
     * @param BanksData $bankData
     * @return BankDataResource
     */
    public function show(BanksData $bankData): BankDataResource
    {
        return BankDataResource::make($bankData);
    }

    /**
     * StoreRequest bank data
     * @param StoreRequest $request
     * @return BankDataResource
     */
    public function store(StoreRequest $request): BankDataResource
    {
        //validate
        $validated = $request->validated();
        $bankData = BanksData::create($validated);

        return BankDataResource::make($bankData);
    }

    /**
     * Update banks data
     * @param BanksData $bankData
     * @param UpdateRequest $request
     * @return BankDataResource
     */
    public function update(BanksData $bankData, UpdateRequest $request): BankDataResource
    {
        //validate
        $validated = $request->validated();
        $bankData->update($validated);

        return BankDataResource::make($bankData->refresh());
    }
}
