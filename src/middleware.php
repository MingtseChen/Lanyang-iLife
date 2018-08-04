<?php
// Application middleware
include "Middleware/SSO.php";

// e.g: $app->add(new \Slim\Csrf\Guard);

//whoops support (should be first to inject with)
$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);

//session
$app->add(new \Slim\Middleware\Session([
    'name' => 'SSID',
    'autorefresh' => false,
    'lifetime' => '1 hour'
]));

//debug bar
$app->add(new RunTracy\Middlewares\TracyMiddleware($app));

$app->add(new SSO());