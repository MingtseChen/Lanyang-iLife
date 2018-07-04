<?php

include_once(__DIR__ . '/../vendor/autoload.php');

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new \Klein\Klein();

$router->respond('/', function ($request, $response, $service) {
    $service->render('views/home.php');
});


$router->dispatch();