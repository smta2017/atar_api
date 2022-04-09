<?php

declare(strict_types=1);

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SocialAuthController;
use App\Http\Controllers\Api\Authorize\AuthorizeController;
use App\Http\Controllers\API\UserAPIController;
// use App\Http\Controllers\API\UserAPIController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::group([
    'middleware' => [
        'web',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class
    ]
], function () {
    Route::middleware(['universal'])->group(function () {
        Auth::routes();
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
        });
    });
});


Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api')->group(function () {
    Route::group(['prefix' => 'v1'], function () {

        Route::group(['prefix' => 'auth'], function () {

            Route::post('login', [AuthController::class, 'login']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('me', [AuthController::class, 'me']);

            Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
            Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'resetView'])->middleware('guest')->name('password.reset');
            Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

            // google
            Route::get('/google/login', [SocialAuthController::class, 'googleLogin']);
            Route::get('/google/callback', [SocialAuthController::class, 'googleCallback']);
            // facebook
            // Route::get('/facebook/login', [SocialAuthController::class, 'facebookLogin']);
            // Route::get('/facebook/callback', [SocialAuthController::class, 'facebookCallback']);

        });

        Route::group(['middleware' => 'auth:sanctum'], function () {

            Route::group(['prefix' => 'authorize'], function () {

                Route::get('/roles', [AuthorizeController::class, 'roles']);
                Route::post('/roles', [AuthorizeController::class, 'createRole']);
                Route::get('/permissions', [AuthorizeController::class, 'permissions']);
                Route::post('/permissions', [AuthorizeController::class, 'createPermission']);
                Route::post('/permission-to-role', [AuthorizeController::class, 'assignRoleToPermission']);
                Route::get('/role-permissions/{id}', [AuthorizeController::class, 'rolePermissions']);
                Route::post('/role-to-user', [AuthorizeController::class, 'assignRoleToUser']);
                Route::get('/user/{id}/permissions', [AuthorizeController::class, 'userPermissions']);
                Route::post('/revoke-permission', [AuthorizeController::class, 'revokePermission']);
                
            });

            Route::apiResource('users', UserAPIController::class);
        });

    });
});
