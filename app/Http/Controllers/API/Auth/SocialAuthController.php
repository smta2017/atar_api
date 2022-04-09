<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\AppBaseController;
use App\Models\User;
use Socialite;
use Exception;
use Carbon\Carbon;

class SocialAuthController extends AppBaseController
{



    public function googleLogin()
    {
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return  $this->sendResponse($url, "success");
    }

    public function googleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            $createUser = User::firstOrCreate([
                'google_id' => $user->id
            ], [
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => Carbon::now()->toDateTimeString(),
                'google_id' => $user->id,
                'password' => \Hash::make('adminatarcloud@123')
            ]);
            $token = $createUser->createToken('google')->plainTextToken;

            return $this->sendResponse(['access_token' => $token, 'token_type' => 'bearer'], "success");
        } catch (Exception $exception) {
            $exception->getMessage();
        }
    }

    // public function facebookLogin()
    // {
    //     $url = Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl();
    //     return  $this->sendResponse($url, "success");
    // }

    // public function facebookCallback()
    // {
    //     try {
    //         $user = Socialite::driver('facebook')->stateless()->user();

    //         $createUser = User::firstOrCreate([
    //             'facebook_id' => $user->id
    //         ], [
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'email_verified_at' => Carbon::now()->toDateTimeString(),
    //             'facebook_id' => $user->id,
    //             'password' => encrypt('admin@123')
    //         ]);
    //         $token = $createUser->createToken('token-name')->plainTextToken;

    //         return \response()->json(['access_token' => $token, 'token_type' => 'bearer'], 200);
    //     } catch (Exception $exception) {
    //         dd($exception->getMessage());
    //     }
    // }
}
