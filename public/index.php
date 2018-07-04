<?php

include_once(__DIR__ . '/../vendor/autoload.php');

//setup whoops error handler
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new \Klein\Klein();

//set layout page
$router->respond(function ($request, $response, $service) {
    $service->layout('views/layouts/default.php');
});

$router->respond('/', function ($request, $response, $service) {
    $service->render('views/home.php');
});

$router->respond('GET', '/login', function ($request, $response, $service) {
    $service->render('views/auth/login.php');
});


$router->dispatch();