<?php
#TODO : Add Controller
use Slim\Http\Request;
use Slim\Http\Response;

// Routes

//$app->get('/', function (Request $request, Response $response, array $args) {
//    // Sample log message
//    $this->logger->info("'/' route");
//    // Render index view
//    return $this->renderer->render($response, 'home.php', $args);
//});

//Home
$app->get('/', function (Request $request, Response $response, $args) {
    $loginStatus = $this->auth->isLogin();
    if ($loginStatus) {
        $id = $this->auth->sso_userid();
        $username = $this->db->fetch($id)->getUsername();
        return $this->view->render($response, 'home.twig', [
            'login' => $loginStatus,
            'id' => $id,
            'name' => $username
        ]);
    } else {
        return $this->view->render($response, 'home.twig', [
            'login' => $loginStatus,
        ]);
    }
})->setName('home');
//login
$app->group('/login', function ($app) {
    $app->get('', function ($request, $response, $args) {
        return $this->view->render($response, '/auth/login.twig');
    })->setName('loginForm');
    $app->post('/auth', function ($request, $response, $args) {
        return 'dddd';
    })->setName('loginAuth');
});

//console
$app->post('/console', 'RunTracy\Controllers\RunTracyConsole:index');
