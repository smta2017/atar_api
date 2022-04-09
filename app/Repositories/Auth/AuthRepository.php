<?php

namespace App\Repositories\Auth;

use App\Helpers\ApiResponse;
use App\Models\SpecialistArea;
use App\Models\SpecialistType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Container\Container as Container;

/**
 * Class AuthRepository
 */
class AuthRepository
{

    /**
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }



    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'data' => [
                "token" => $token,
                "private_policy_accepted" => false,
                "user_id" => auth()->user()->id
            ],
            // 'access_token' => $token,
            // 'token_type' => 'Bearer',
            // 'full_token' => 'Bearer ' . $token,
            // 'user' => auth()->user()
        ]);
    }


    /**
     * @param $request
    //  * @return Application|ResponseFactory|Response
     */
    public function loginUser($request)
    {
        // login by phone or email
        if (is_numeric($request['email'])) {
            $request = ['phone' => $request['email'], 'password' => $request['password']];
        }

        if (!auth()->attempt($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken(auth()->user()->createToken('')->plainTextToken);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function registerUser($request)
    {
        // \DB::beginTransaction();

        try {
            $tenant = \App\Models\Tenant::create([
                'id' => $request->domain,
                'plan' =>  $request->plan,
                'email' =>  $request->email,
                'phone' =>  $request->phone,
            ]);

            $tenant->domains()->create([
                'domain' => $request->domain . '.' . config('tenancy.central_domains')[0],
            ]);

            $tenant->run(function () use ($request) {
                User::create(array_merge(
                    $request->all(),
                    ['password' => Hash::make($request->password), 'name' => $request->name . '-' . $request->domain],
                ));
            });

            $tenantUser = [
                'tenant' => $request->domain,
                'email' => $request->email,
                'phone' => $request->phone,
                'domain' => $request->domain,
                'google_token' => $request->google_token,
            ];

            $userRepository = new UserRepository(new Container());

            $userRepository->storeCentralTenantUser($tenantUser);

            // \DB::commit();
            // all good
        } catch (\Exception $e) {
            // \DB::rollback();
            // something went wrong
        }

        return ['tenant' => $tenant];
        //==========================================


        // try {
        //     //need job
        //     if (\config("app.enable_email_verification")) {
        //         $user->sendEmailVerificationNotification();
        //     }
        //     //need job
        //     if (\config("app.enable_phone_verification")) {
        //         $user->sendPhoneVerificationOTP();
        //     }
        // } catch (\Throwable $th) {
        //     return $user;
        // }

    }


    public function addUserArea($user_id, $area_id)
    {
        SpecialistArea::create(['user_id' => $user_id, 'area_id' => $area_id]);
    }

    public function addUserSpecial($user_id, $area_id)
    {
        SpecialistType::create(['user_id' => $user_id, 'special_type_id' => $area_id]);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function changePassword($request): JsonResponse
    {
        return \response()->json("ok");
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), ['email' => 'required|email']);

        if ($validator) {
            // return ApiResponse::apiFormatValidation($validator);
        }

        $status = Password::sendResetLink(
            ['email' => 'alysha57@example.net']
        );

        return  "Email sent.";
    }
    public function resetView(Request $request)
    {
        return \view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator) {
            return ApiResponse::apiFormatValidation($validator);
        }
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return \view("auth.succses-reset-password");
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function verifyEmail($request): JsonResponse
    {
        return \response()->json($request);
    }
    /**
     * @param $request
     * @return JsonResponse
     */
    public function verifiedPhone($request): JsonResponse
    {
        return \response()->json("ok");
    }

    /**
     * Send OTP for user for verification
     * @param $request
     * @return JsonResponse
     */
    public function sendCode($request): JsonResponse
    {
        return \response()->json("ok");
    }



    public function verifyCode($request)
    {
        return \response()->json("ok");
    }


    /**
     * @return JsonResponse
     */
    public function getProfile(): JsonResponse
    {
        return \response()->json("ok");
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function editProfile($request): JsonResponse
    {
        return \response()->json("ok");
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function logout($user): JsonResponse
    {
        return  ApiResponse::format("success", $user->tokens()->delete());
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function updateDeviceToken($request): JsonResponse
    {
        return \response()->json("ok");
    }
}
