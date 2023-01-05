<?php

namespace App\Helpers;
use App\Helpers\Curl;

class Push
{
    public static $headers;
    public static $contents;
    public static $devices;
    public static $url;
    public static $group;
    public static $icon;

    private static $_instance = null;

    private function __construct () { }

    public static function init ()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function headers($headers)
    {
        self::$headers = $headers;
        return $this;
    }
    public function contents($contents)
    {
        self::$contents = $contents;
        return $this; 
    }
    public function devices($devices)
    {
        self::$devices = $devices;
        return $this; 
    }
    public function url($url)
    {
        self::$url = $url;
        return $this; 
    }
    public function group($group)
    {
        self::$group = $group;
        return $this; 
    }
    public function icon($icon)
    {
        self::$icon = $icon;
        return $this; 
    }

    public function to($user)
    {
        if ($user == 'customer') {
            $app_id = '0e95a1b5-5c06-4445-99e6-33a1267bb58d';
        } elseif ('seller') {
            $app_id = 'ee3fb2ee-d698-4220-b5b6-6a67fcecf8c3';
        }
        $fields = array(
            'app_id' => $app_id,
            'include_player_ids' => self::$devices,
            'data' => array("foo" => "bar"),
            'headings' => ["en" => self::$headers],
            'contents' => ["en" => self::$contents],
            'app_url' => self::$url,
        );
        if (self::$icon != null) {
            $fields['large_icon'] = self::$icon;
        }
        if (self::$group != null) {
            $fields['android_group'] = self::$group;
        }
        $url = 'https://onesignal.com/api/v1/notifications';
        $header = array('Content-Type: application/json; charset=utf-8');
        $body = json_encode($fields);
        return Curl::post($url, $header, $body);
    }
}