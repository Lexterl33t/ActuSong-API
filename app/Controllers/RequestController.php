<?php

namespace App\Controllers;

class RequestController
{
    public static function post($url, $headers = null, $params = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        if($params)
            curl_setopt($ch, CURLOPT_POSTFIELDS,$params);


        if($headers)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);


        return $server_output;
    }

    public static function get($url, $headers = null, $params = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url.'?'.http_build_query($params));


        if($headers)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);

        return $server_output;
    }
}