<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

//whoops support (should be first to inject with)
$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);

$app->add(new RunTracy\Middlewares\TracyMiddleware($app));