<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;


class AddressController extends Controller
{
    /**
     * Getting user's addresses
     * @return AnonymousResourceCollection
     */
    public function fetchByUser(): AnonymousResourceCollection
    {
        $user = Auth::user();

        $address = $user->address()->get();

        return AddressResource::collection($address);
    }

    /**
     * Show address
     * @param Address $address
     * @return AddressResource
     */
    public function show(Address $address): AddressResource
    {
        return AddressResource::make($address);
    }

    /**
     * StoreRequest address details
     * @param AddressRequest $request
     * @return AddressResource
     */
    public function store(AddressRequest $request): AddressResource
    {
        $validated = $request->validated();
        $validated['coords'] = json_encode($validated['coords']);

        $address = Address::create($validated);

        return AddressResource::make($address);
    }

    /**
     * Update addresses value
     * @param Address $address
     * @param AddressRequest $request
     * @return AddressResource
     */
    public function update(Address $address, AddressRequest $request): AddressResource
    {
        $validated = $request->validated();
        $validated['coords'] = json_encode($validated['coords']);

        $address->update($validated);

        return AddressResource::make($address->refresh());
    }
}
