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
    //sso login check
//    $ssoLoginCheck = $this->auth->isSsoLogin();
    //TODO Clean Home Page
//    $ssoLoginCheck = true;
//    if ($ssoLoginCheck) {
//        $student = new Student();
//        $id = $this->auth->sso_userid();
//        $id = 403840308;
//        $username = $student->fetch($id)->getUsername();
//    }
//        $username = $this->db->fetch($id)->getUsername();
    $id = 'test';
    if ($this->session->exists('id')) {
        $id = $this->session->id;
    }
    $data = [
        'login' => true,
        'id' => 403840308,
        'name' => $id
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
        return $this->view->render($response, '/bus/reserve.twig', $schedule);
    })->setName('busSearch');

    $app->post('/reserve', function ($request, $response, $args) {
        var_dump($request->getParsedBody());
    })->setName('busReserve');
});

//login
$app->group('/login', function ($app) {

    $app->get('', function ($request, $response, $args) {
        return $this->view->render($response, '/auth/login.twig');
    })->setName('loginForm');

    //login
    $app->post('', function ($request, $response, $args) {
        $passport = new User();
        $loginData = $request->getParsedBody();
        if ($passport->login($loginData)) {
            //success
            $this->session->set('uname', $loginData['uname']);
            $this->flash->addMessage('success', 'success');
            return $response->withRedirect('/');
        } else {
            //fail
//            $this->flash->addMessage('msg', 'username or password incorrect');
            return $this->view->render($response, '/auth/login.twig', ['error' => true]);
        }
    })->setName('loginAuth');

    $app->get('/recover', function ($request, $response, $args) {
        return $this->view->render($response, '/auth/forget.twig');
    })->setName('recover');

});

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
