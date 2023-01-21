<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function __construct()
    {
        $this->user = auth('api')->user();
    }
    
    public function index()
    {
        return $this->user;
    }
}
