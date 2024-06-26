<?php

namespace App\Http\Controllers\Api\Auth;

use Alexvexone\LaravelOperSms\OperSmsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\PhoneNumberRequest;
use App\Models\User;
use App\Models\VerifyCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class OTPController extends Controller
{
    /**
     * Sending code to user phone number
     * @param PhoneNumberRequest $request
     * @return JsonResponse
     */
    public function sendOTP(PhoneNumberRequest $request): JsonResponse
    {
        //validate
        $validated = $request->validated();

        //random code
        $code = rand(100000, 999999);
        if ($validated['phone_number'] == '998111111111') {
            $code = 111111;
        }

        $proUser = User::query()->where('phone_number', $validated['phone_number'])->first();
        $validated['email'] = null;
        if ($proUser) {
            $validated['email'] = $proUser['email'];

            if (!empty($proUser['email'])) {
                Mail::to($validated['email'])->send(new \App\Mail\VerifyCode($code));
            }
        }

        //text for sent user phone number
        $sentText = "PRO:" . $code . "– Ваш одноразовый код в PRO ID.";

        //sending text
        $sent = OperSmsService::send($validated['phone_number'], $sentText);

        if (gettype($sent) == "array") {

            //saving sending code in db
            $verifyCode = VerifyCode::query()->create([
                'phone_number' => $validated['phone_number'],
                'code' => $code,
                'email' => $validated['email']
            ]);

            //return response
            return new JsonResponse($verifyCode, 200);
        } else {
            abort(429);
        }
    }

    /**
     * Checking written code
     * @param PhoneNumberRequest $request
     * @return JsonResponse
     */
    public function checkCode(PhoneNumberRequest $request): JsonResponse
    {
        $validated = $request->validated();

        //getting sending code by phone number
        $phone = VerifyCode::query()->where('phone_number', $validated['phone_number'])->latest()->first();

        if ($phone && (int)$phone->code === (int)$request->input('code'))
            return new JsonResponse(['status' => true], Response::HTTP_OK);

        //return error
        return new JsonResponse(['status' => false, 'message' => 'Invalid code'], Response::HTTP_FORBIDDEN);
    }

}
