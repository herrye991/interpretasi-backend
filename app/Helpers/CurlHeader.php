<?php

namespace App\Helpers;

class CurlHeader
{
    public static function post($url = null, $header = [], $body = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_NOBODY => true,
            CURLOPT_HEADERFUNCTION => 'header_callback'
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:$err";
        } else {
            $response = json_decode($response,true);
            return $response;
        }
    }

    public static function get($url = null, $header = [])
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER, true,
            CURLOPT_NOBODY => true,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:$err";
        } else {
            $response = json_decode($response,true);
            return $response;
        }
    }

    public static function delete($url = null, $header = [], $body = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_NOBODY => true,
            CURLOPT_HEADERFUNCTION => 'header_callback'
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:$err";
        } else {
            $response = json_decode($response,true);
            return $response;
        }
    }

    public static function put($url = null, $header = [], $body = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_NOBODY => true,
            CURLOPT_HEADERFUNCTION => 'header_callback'
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:$err";
        } else {
            $response = json_decode($response,true);
            return $response;
        }
    }
}