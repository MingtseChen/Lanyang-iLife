<?php
// Application middleware
include "Middleware/SSO.php";

use Psr7Middlewares\Middleware\TrailingSlash;

// e.g: $app->add(new \Slim\Csrf\Guard);

//whoops support (should be first to inject with)
$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);

//Debug bar
$app->add(new RunTracy\Middlewares\TracyMiddleware($app));

//SSO Login
$app->add(new SSO());

//Session
//$app->add(new \Slim\Middleware\Session([
//    'name' => 'SSID',
//    'autorefresh' => true,
//    'lifetime' => '1 hour'
//]));

$app->add(new TrailingSlash(false));