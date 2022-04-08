<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\User\IAuth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /**
     * @var IAuth
     */
    protected $auth;

    /**
     * AuthController constructor.
     * @param IAuth $auth
     */
    public function __construct(IAuth $auth)
    {
        return $this->auth = $auth;
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *  * @SWG\Post(
     *      path="/auth/forgot-password",
     *      summary="send user reset password email",
     *      tags={"Auth"},
     *      description="the system users login",
     *      produces={"application/json"},
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
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      ),
     * 
     *  @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerAddress that should be updated",
     *          required=false,
     *          @SWG\Schema(example= 
     *               {
     *                "email":"esteban55@example.com",
     *               }
     *          )
     *      ),
     * 
     * )
     */

    public function forgotPassword(Request $request)
    {
        $data = $this->auth->forgotPassword($request);
        return $data;
        return  ApiResponse::format( "success", $data,'Email sent.');
    }


    public function resetView(Request $request)
    {
        return $this->auth->resetView($request);
    }

    public function resetPassword(Request $request)
    {
        return $this->auth->resetPassword($request);
    }
}
