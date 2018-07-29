<?php
//TODO : Add Controller

use Respect\Validation\Validator as v;

include 'Models/StudentModel.php';
include 'Models/UserModel.php';
include 'Models/BusModel.php';
include 'Middleware/AdminSection.php';
include 'Middleware/RedirectIfAuth.php';

//use Slim\Http\Request;
//use Slim\Http\Response;

// Routes

//$app->get('/', function (Request $request, Response $response, array $args) {
//    // Sample log message
//    $this->logger->info("'/' route");
//    // Render index view
//    return $this->renderer->render($response, 'home.php', $args);
//});

//Home

$app->get('/', function ($request, $response, $args) {

    $id = 'null';
    $name = 's';
    $login = false;
    if ($this->session->exists('id')) {
        $id = $this->session->id;
        $name = $this->session->name;
        $login = true;
    } else {
        $login = false;
    }
    $data = [
        'login' => $login,
        'id' => $id,
        'name' => $name
    ];
    return $this->view->render($response, 'home.twig', $data);
})->setName('home');

//bus
$app->group('/bus', function ($app) {

    $app->get('', function ($request, $response, $args) {
        return $this->view->render($response, '/bus/search.twig');
    })->setName('busIndex');

    $app->get('/search', function ($request, $response, $args) {
        $from = $request->getQueryParams()['from'];
        $date = $request->getQueryParams()['date'];
        $bus = new Bus();
        $schedule = ['schedules' => $bus->find($from, $date)];
        $uid = $this->session->id;

        if ($bus->isSuspend($uid)) {
            return $response->withRedirect('/bus/status?action=fail&status=suspend');
        } else {
            if (empty($schedule['schedules'])) {
                return $response->withRedirect('/bus/status?action=false&status=null');
            }
        }

        return $this->view->render($response, '/bus/reserve.twig', $schedule);
    })->setName('busSearch');

    $app->post('/reserve', function ($request, $response, $args) {
        $post = $request->getParsedBody();
        $bus = new Bus();
        $uid = $this->session->id;
        $name = $this->session->name;

        if (!isset($post['schedule'])) {
            return $response->withRedirect('/bus/status?status=null');
        } else {
            $status = $bus->reserve($post['schedule'], $uid, $name, $post['dept'], $post['room']);
        }
        if ($status) {
            return $response->withRedirect('/bus/status?action=success');
        } else {
            return $response->withRedirect('/bus/status?action=fail');
        }

    })->setName('busReserve');

    $app->get('/status', function ($request, $response, $args) {
//        $result = $request->getQueryParams()['action'];
        return $this->view->render($response, '/bus/status.twig');
    })->setName('busStatus');
});

//login
$app->group('/login', function ($app) {

    $app->get('', function ($request, $response, $args) {
        return $this->view->render($response, '/auth/login.twig');
    })->setName('loginForm');

    $app->get('/recover', function ($request, $response, $args) {
        return $this->view->render($response, '/auth/forget.twig');
    })->setName('recover');

});

$app->get('/logout', function ($request, $response, $args) {
    $this->session::destroy();
    return $response->withRedirect('/');
})->setName('recover');

//admin

$app->group('/admin', function ($app) {
    $app->get('', function ($request, $response, $args) {
        return $this->view->render($response, '/admin/index.twig');
    })->setName('admin');

    $app->group('/users', function ($app) {
        $app->get('', function ($request, $response, $args) {
            $admin = new User();
            $student = new Student();
            $adminData = $admin->read();
            $students = $student->read();
            $data = [
                'users' => $adminData,
                'students' => $students,
                'student_count' => $admin->statistic()[0],
                'user_count' => $admin->statistic()[1],
                'user_active' => $admin->statistic()[3],
            ];
            return $this->view->render($response, '/admin/user.show.twig', $data);
        })->setName('users');

        $app->get('/create', function ($request, $response, $args) {
            return $this->view->render($response, '/admin/user.create.twig');
        })->setName('create');

        //Validate Rules
        $uid = v::digit()->noWhitespace()->length(1, 10);
        $validators = array(
            'uid' => $uid,
        );
        $app->post('/create/submit', function ($request, $response, $args) {
            $user = new User();
            $userData = $request->getParsedBody();
            if ($request->getAttribute('has_errors')) {
                $errors = $request->getAttribute('errors');
                //var_dump($errors);
                $msg = 'dddd';
                $this->flash->addMessage('msg', $msg);
                return $response->withRedirect('/admin/users/create');
            } elseif ($user->usernameIsValid($userData)) {
                $user->create($userData);
                $msg = 'Success';
                $this->flash->addMessage('msg', $msg);
                return $response->withRedirect('/admin/users');
            } else {
                $this->flash->addMessage('msg', 'error');
                return $response->withRedirect('/admin/users/create');
            }
        })->setName('submituser')->add(new \DavidePastore\Slim\Validation\Validation($validators));
    });
});

//console
$app->post('/console', 'RunTracy\Controllers\RunTracyConsole:index');
