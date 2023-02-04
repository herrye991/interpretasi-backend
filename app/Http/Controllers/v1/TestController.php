<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Helpers\Curl;
use App\Helpers\Path;
use Carbon\Carbon;
use URL;

class TestController extends Controller
{
    public function index()
    {
        return Path::public('');
    }
}
