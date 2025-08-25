<?php

echo "<pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "cURL Extension Loaded: " . (extension_loaded('curl') ? 'Yes' : 'No') . "\n\n";

if (!extension_loaded('curl')) {
    echo "CRITICAL ERROR: cURL extension is not installed or enabled in your PHP configuration.\n";
    exit;
}

$test_url = 'https://www.google.com';
echo "--- Testing Connection to: " . $test_url . " ---\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $test_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$output = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "cURL Error: " . $error . "\n";
} else {
    echo "Success! Connected to Google successfully.\n";
}

echo "\n--- Now Testing Connection to Packzy API ---\n";

$packzy_url = 'https://portal.packzy.com/api/v1/fraud_check/01960408315';
$ch_packzy = curl_init();
curl_setopt($ch_packzy, CURLOPT_URL, $packzy_url);
curl_setopt($ch_packzy, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_packzy, CURLOPT_TIMEOUT, 15);
$output_packzy = curl_exec($ch_packzy);
$error_packzy = curl_error($ch_packzy);
curl_close($ch_packzy);

if ($error_packzy) {
    echo "cURL Error for Packzy: " . $error_packzy . "\n";
} else {
    echo "Success! Connected to Packzy API successfully.\n";
}

echo "</pre>";
