<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Getting user's addresses
     * @param User $user
     * @return JsonResponse
     */
    public function fetchByUser(User $user): JsonResponse
    {
        //addresses
        $address = $user->address()->get();

        //return response
        return new JsonResponse($address);
    }

    /**
     * Show address
     * @param Address $address
     * @return JsonResponse
     */
    public function show(Address $address): JsonResponse
    {
        return new JsonResponse($address);
    }

    /**
     * Store address details
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string',
            'coords' => 'required'
        ]);

        //set coords array to json
        $validated['coords'] = json_encode($validated['coords']);

        //store address in db
        $address = Address::query()->create($validated);

        //return response
        return new JsonResponse($address);
    }

    /**
     * Update addresses value
     * @param Address $address
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Address $address, Request $request): JsonResponse
    {
        //validate
        $validated = $request->validate([
            'name' => 'required|string',
            'coords' => 'required'
        ]);

        //set coords array to json
        $validated['coords'] = json_encode($validated['coords']);

        //update value
        $address->update($validated);

        //return response
        return new JsonResponse($address);
    }
}
