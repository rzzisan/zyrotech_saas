<?php

// START: চূড়ান্ত ডিবাগিং কোড
//--------------------------------------------------------------------------
// এই কোডটি সকল ইনকামিং হেডারের তথ্য একটি লগ ফাইলে লিখে রাখবে
//--------------------------------------------------------------------------
$headers = [];
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        $headerKey = str_replace('_', '-', substr($key, 5));
        $headers[$headerKey] = $value;
    }
}
$log_entry = "Time: " . date('Y-m-d H:i:s') . "\n";
$log_entry .= "URL: " . $_SERVER['REQUEST_URI'] . "\n";
$log_entry .= "Headers: " . json_encode($headers) . "\n\n";
file_put_contents(__DIR__.'/../storage/logs/headers.log', $log_entry, FILE_APPEND);
//--------------------------------------------------------------------------
// END: চূড়ান্ত ডিবাগিং কোড
//--------------------------------------------------------------------------


use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());