<?php

namespace App\Helpers;

class Path
{
    public static function home($parrent = null)
    {
        return '/home/u362723321/' . $parrent;
    }

    public static function root($parrent = null)
    {
        return '/home/u362723321/domains/interpretasi.id/api/' .  $parrent;
    }

    public static function public($parrent = null)
    {
        return '/home/u362723321/domains/interpretasi.id/public_html/' .  $parrent;
    }

}