<?php

namespace App\Http\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    public static function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public static function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    public function sendSuccess($message)
    {
        return Response::json([
            'success' => true,
            'message' => $message
        ], 200);
    }

    public static function apiFormatValidation($validator)
    {
        $errors = self::convertValidationErrors($validator->errors());
        $response = self::sendError($errors);
        throw new HttpResponseException($response);
    }

    public static function convertValidationErrors($errors)
    {
        $err_msg = [];
        foreach ($errors->messages() as $key => $value) {
            $err_msg[$key] = $value[0];
        };
        return $err_msg;
    }
}
