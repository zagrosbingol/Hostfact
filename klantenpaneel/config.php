<?php
$configuration = array();

// PHP Sessions will use the Samesite=Lax value. You can set this to a different value (None, Strict or Lax)
// $configuration['session']['samesite'] = 'Lax';

// API calls via cURL are limited to 10 seconds by default. Sometimes, it might be necessarry to increase this limit, for example when there are clients with a lot of domains in their hosting accounts.
// $configuration['curl']['timeout'] = '10'; // Timout in seconds

$configuration['cache']['method'] 		= 'session'; // session || memcache
$configuration['cache']['caching_time'] = 120; // Time in seconds to cache

$configuration['cache']['memcache']['host'] = 'localhost'; // Host or IP memcache server
$configuration['cache']['memcache']['port'] = 11211; // Port memcache server

// Some security prevention, some are inactive by default, because misconfiguration can prevent your orderform or images to be loaded.
/**
 * Security headers
 * See https://www.owasp.org/index.php/Security_Headers for more details.
 */
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

/**
 * Clickjacking prevention
 * See https://www.owasp.org/index.php/Clickjacking for more details.
 */
// header('X-Frame-Options: SAMEORIGIN');

/**
 * Content Security Policy
 * See https://www.owasp.org/index.php/Content_Security_Policy for more details.
 */
//$csp_policy = "default-src 'self' 'unsafe-inline' 'unsafe-eval'; img-src 'self' data: chart.googleapis.com;";
//header("Content-Security-Policy: ".$csp_policy);
//header("X-Content-Security-Policy: ".$csp_policy);
//header("X-WebKit-CSP: ".$csp_policy);

?>