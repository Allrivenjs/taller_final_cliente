<?php

namespace Config\Cors;

final class Cors
{
    public final static function cors(): void
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header('Access-Control-Allow-Origin: *');
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS");
                header('Access-Control-Allow-Headers: token, Content-Type');
                header('Access-Control-Max-Age: 1728000');
                header('Content-Length: 0');
                header('Content-Type: text/plain');
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }
}