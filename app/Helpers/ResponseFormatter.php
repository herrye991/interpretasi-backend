<?php

namespace App\Helpers;

/**
 * ResponseFormatter
 */
class ResponseFormatter
{
  protected static $response = [
    'code' => 200,
    'message' => null,
    'data' => null
  ];
  
  /**
   * success
   *
   * @param  mixed $data
   * @param  mixed $message
   * @param  mixed $code
   * @return void
   */
  public static function success($data = null, $message = null, $code = null)
  {
    self::$response['message'] = 'success';
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code']);
  }
  
  /**
   * error
   *
   * @param  mixed $data
   * @param  mixed $message
   * @param  mixed $code
   * @return void
   */
  public static function error($data = null, $message = null, $code = 400)
  {
    self::$response['code'] = $code;
    self::$response['message'] = 'error';
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['code']);
  }
}