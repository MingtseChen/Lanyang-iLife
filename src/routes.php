<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("'/' route");
    // Render index view
    return $this->renderer->render($response, 'home.php', $args);
});


//$app->get('', $controller('index'));
////$controller = new App\Controller\RootController($app);
//$app->get('/', $this->controller('index'));
//
//$app->get('/hello/{name}', function ($request, $response, $args) {
//    return $this->view->render($response, 'profile.html', [
//        'name' => $args['name']
//    ]);
//})->setName('profile');