<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\Curl;
use Carbon\Carbon;

class TestController extends Controller
{
    public function index()
    {
        $curl = Curl::get('https://worldtimeapi.org/api/timezone/Asia/Jakarta', []);
        return base64_encode(Carbon::createFromTimestamp($curl['unixtime'])->toDateTimeString());
    }
}
