<?php
//TODO : Add Controller

use Respect\Validation\Validator as v;

include 'Models/StudentModel.php';
include 'Models/UserModel.php';
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
    $ssoLoginCheck = $this->auth->isSsoLogin();
    //user login check
    $userLoginCheck = $this->auth->isUserLogin();

    //TODO Clean Home Page
//    $ssoLoginCheck = true;
    if ($ssoLoginCheck) {
        $student = new Student();
        $id = $this->auth->sso_userid();
        $username = $student->fetch($id)->getUsername();
//        $username = $this->db->fetch($id)->getUsername();
        return $this->view->render($response, 'home.twig', [
            'login' => $ssoLoginCheck,
            'id' => $id,
            'name' => $username
        ]);
    } else {
        return $this->view->render($response, 'home.twig', [
            'login' => $ssoLoginCheck,
        ]);
    }
})->setName('home');

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

})->add(new RedirectIfAuth());

//admin

$app->group('/admin', function ($app) {
    $app->get('', function ($request, $response, $args) {
        return $this->view->render($response, '/admin/index.twig');
    })->setName('admin');

    $app->group('/manage', function ($app) {
        $app->get('/privilege', function ($request, $response, $args) {
            $user = new User();
            $student = new Student();
            $users = $user->read();
            $students = $student->read();
            return $this->view->render($response, '/admin/privilege.setting.twig',
                [
                    'users' => $users,
                    'students' => $students,
                    'student_count' => $user->statistic()[0],
                    'user_count' => $user->statistic()[1],
                    'user_active' => $user->statistic()[3],
                ]);
        })->setName('privilege');
        $app->get('/create', function ($request, $response, $args) {
            return $this->view->render($response, '/admin/user.create.twig');
        })->setName('create');

        //Validate Rules
//        TODO enforce password security
        $name = v::noWhitespace()->length(1, 32);
        $username = v::alnum()->noWhitespace()->length(1, 32);
        $email = v::email()->length(1, 48);
        $password = v::alnum()->noWhitespace()->length(7, 32);
        $validators = array(
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'pwd' => $password,
        );
        $app->post('/create/submit', function ($request, $response, $args) {
            $user = new User();
            $userData = $request->getParsedBody();
            if ($request->getAttribute('has_errors')) {
                $errors = $request->getAttribute('errors');
                //var_dump($errors);
                $msg = 'dddd';
                $this->flash->addMessage('error', $msg);
                return $response->withRedirect('/admin/manage/create');
            } elseif ($user->usernameIsValid($userData)) {
                $user->create($userData);
                $msg = 'Success';
                $this->flash->addMessage('msg', $msg);
                return $response->withRedirect('/admin/manage/privilege');
            } else {
                $this->flash->addMessage('msg', 'error');
                return $response->withRedirect('/admin/manage/create');
            }
        })->setName('submituser')->add(new \DavidePastore\Slim\Validation\Validation($validators));
    });
})->add(new AdminSection());

//console
$app->post('/console', 'RunTracy\Controllers\RunTracyConsole:index');
