<?php

namespace App\Http\Controllers;

use App\Mail\VerifyCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendEmailController extends Controller
{
    /**
     * Sending confirmation code to user email
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmail(Request $request): JsonResponse
    {
        //validate email
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        //if validated
        if($validated){
            //generate code
            $verificationCode = rand(100000, 999999);

            //send email
            Mail::to($validated['email'])->send(new VerifyCode($verificationCode));

            //return Response
            return new JsonResponse($verificationCode);
        }else{
            abort(400);
        }
    }
}
