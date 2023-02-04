<?php

namespace App\Helpers;

class Domain
{
    public static function base($path = '/')
    {
        return 'https://interpretasi.id/'.$path;
    }
}