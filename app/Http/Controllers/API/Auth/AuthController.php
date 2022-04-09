<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\TenantUser;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Http\Request;
use Auth;

class AuthController extends AppBaseController
{

    protected $auth;

    /**
     * AuthController constructor.
     * @param IAuth $auth
     */
    public function __construct(AuthRepository $auth)
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register','getDomain']]);
        return $this->auth = $auth;
    }



    public function login()
    {
        $credentials = request(['email', 'password']);
        return  $this->auth->loginUser($credentials);
    }

    public function getDomain(Request $request)
    {
        return $this->sendResponse(TenantUser::where('email', $request["email"])->first(), 'sucsess');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validate([
            'email' => 'required|unique:tenant_users|max:255',
            'phone' => 'required|unique:tenant_users|max:50',
            'domain' => 'required|unique:tenant_users|max:255',
        ]);

        $user = $this->auth->registerUser($request);
        return  $this->sendResponse($user, "Success!");
    }

    public function me()
    {
        // return "IM";
        return $this->sendResponse(new UserResource(auth()->user()), "sucsess");
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        return $this->auth->logout(auth()->user());
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->user()->createToken('')->plainTextToken);
    }

    public function forgotPassword(Request $request)
    {
        return $this->auth->forgotPassword($request);
    }
}
