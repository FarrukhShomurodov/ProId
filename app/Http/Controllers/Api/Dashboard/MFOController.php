<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MFO;
use Illuminate\Http\JsonResponse;

class MFOController extends Controller
{
    /**
     * Fetching all banks data
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $bankData = MFO::query()->get();
        return new JsonResponse($bankData);
    }
}
