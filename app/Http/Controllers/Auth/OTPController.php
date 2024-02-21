<?php

namespace App\Http\Controllers\Auth;

use Alexvexone\LaravelOperSms\OperSmsService;
use App\Http\Controllers\Controller;
use App\Models\PhoneSMS;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OTPController extends Controller
{
    /**
     * Sending code to user phone number
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sendOTP(Request $request): JsonResponse
    {
        //random code
        $code = rand(100000, 999999);

        //text for sent user phone number
        $sentText = "PRO:" . $code . "– Ваш одноразовый код в PRO ID.";

        //sending text
        $sent = OperSmsService::send($request->phone_number, $sentText);

        if (gettype($sent) == "array") {

            //saving sending code in db
            $phoneSMS = PhoneSMS::query()->create([
                'phone_number' => $request->phone_number,
                'code' => $code,
            ]);

            //return response
            return new JsonResponse($sent, 200);
        } else {
            abort(429);
        }
    }

    /**
     * Checking written code
     * @param Request $request
     * @return JsonResponse
     */
    public function checkCode(Request $request): JsonResponse
    {
        //getting sending code by phone number
        $phone = PhoneSMS::query()->where('phone_number', '=', $request->input('phoneNumber'))->latest()->first();

        //comparing code
        if ($phone && (int)$phone->code === (int)$request->input('code')) {

            //return response
            return new JsonResponse(['success' => true, 'data' => $phone]);
        }

        //return error
        return new JsonResponse(['success' => false, 'message' => 'Invalid code'], [Response::HTTP_FORBIDDEN]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteCode(Request $request): JsonResponse
    {
        $phone = PhoneSMS::query()->where('phone_number', '=', $request->input('phoneNumber'))->latest()->first();
        $phone->delete();
        return new JsonResponse('deleted successfully');
    }

}
