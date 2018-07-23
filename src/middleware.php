<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

//whoops support (should be first to inject with)
$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);

//debug bar
$app->add(new RunTracy\Middlewares\TracyMiddleware($app));

//session
$app->add(new \Slim\Middleware\Session([
    'name' => 'SSID',
    'autorefresh' => true,
    'lifetime' => '1 hour'
]));