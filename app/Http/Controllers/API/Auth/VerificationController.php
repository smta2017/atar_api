<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Traits\SMSTrait;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use SMSTrait;

    /**
     * send phone OTP.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/verify/send-otp/{phone_number}",
     *      summary="Display the specified Subscription",
     *      tags={"Auth"},
     *      description="Send OTP phone verification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="phone_number",
     *          description="phone to send OTP code",
     *          type="string",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function sendMobileOTP($phone_number)
    {
        $res =  $this->sendOTP($phone_number);
        return  ApiResponse::format("success", $res);
    }

    /**
     * verify phone OTP.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/verify/confirm-otp/{phone_number}/{otp}",
     *      summary="Display the specified Subscription",
     *      tags={"Auth"},
     *      description="Verify OTP code",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="phone_number",
     *          description="Verify OTP code",
     *          type="string",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="otp",
     *          description="OTP code to verify",
     *          type="string",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function confirmOTP($phone_number, $otp)
    {
        $res =  $this->verifyOTP($phone_number, $otp);
        if ($res->valid == true) {
            return  ApiResponse::format("success", $res);
        } else {
            return  ApiResponse::format("fail", $res, false);
        }
    }

    public function verifyEmail($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return $this->respondUnAuthorizedRequest(253);
        }
        $user = User::findOrfail($user_id);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return  ApiResponse::format("success", $user);
    }
}
