<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EmailRequest;
use App\Mail\VerifyCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class SendEmailController extends Controller
{
    /**
     * Sending confirmation code to user email
     * @param EmailRequest $request
     * @return JsonResponse
     */
    public function sendEmail(EmailRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $code = rand(100000, 999999);

        $verifyCode = \App\Models\VerifyCode::query()->create([
            'phone_number' => null,
            'code' => $code,
            'email' => $validated['email']
        ]);

        if($verifyCode){
            Mail::to($validated['email'])->send(new VerifyCode($code));

            return new JsonResponse($verifyCode);
        }else{
            abort(400);
        }

    }

    /**
     * Checking written code
     * @param EmailRequest $request
     * @return JsonResponse
     */
    public function checkCode(EmailRequest $request): JsonResponse
    {
        $validated = $request->validated();

        //getting sending code by phone number
        $email = \App\Models\VerifyCode::query()->where('email', $validated)->latest()->first();

        if ($email && (int)$email->code === (int)$request->input('code'))
            return new JsonResponse(['status' => true], Response::HTTP_OK);

        //return error
        return new JsonResponse(['status' => false, 'message' => 'Invalid code'], Response::HTTP_FORBIDDEN);
    }
}
