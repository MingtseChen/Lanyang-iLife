<?php
// DIC configuration

$container = $app->getContainer();

// view controller
//$container['controller'] = function ($app) {
//    $controller = new App\Controller\RootController($app);
//    return $controller;
//};

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// twig engine
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    // Add extensions
    $basePath = rtrim(str_ireplace('index.php', '', $c->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $basePath));
//    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    return $view;
};

// database
require __DIR__ . '/../src/database.php';

$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $db = new Finder($settings);
    return $db;
};

// auth
require __DIR__ . '/../src/auth.php';

$container['auth'] = function () {
    $auth = new Auth();
    return $auth;
};