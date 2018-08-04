<?php
//TODO : Add Controller

use Respect\Validation\Validator as v;

include 'Models/StudentModel.php';
include 'Models/UserModel.php';
include 'Models/BusModel.php';
include 'Models/PackageModel.php';
include 'Plugins/Mail.php';
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

//    $this->flash->addMessage($messages);
//    print_r($messages);
    $id = 'null';
    $name = '';
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
    var_dump($_SESSION);
    return $this->view->render($response, 'home.twig', $data);
})->setName('home');

$app->group('/user', function ($app) {

    $app->get('/index', function ($request, $response, $args) {
        $id = $this->session->id;
        $student = new Student();
        $user = $student->fetch($id);
        $mail = $user->getMail();
        $mail2 = $user->getSecondaryMail();

        $data = ["email" => $mail, 'email2' => $mail2];
        if (isset($request->getQueryParams()['success'])) {
            $this->flash->addMessage('success', 'Info updated');
        }
        return $this->view->render($response, '/user/index.twig', $data);
    })->setName('userIndex');

    $app->post('/index', function ($request, $response, $args) {
        $email = $request->getParsedBody()['mail'];
        $student = new Student();
        $id = $this->session->id;
        $student->addSecondaryMail($id, $email);
        return $response->withRedirect('/user/index?success');
    })->setName('userAddMail');

    $app->get('/bus', function ($request, $response, $args) {
        $bus = new Bus();
        $id = $this->session->id;
        $buses = $bus->readUserBus($id);
        $data = ['buses' => $buses];

        if (isset($request->getQueryParams()['success'])) {
            $this->flash->addMessage('success', 'operation success !');
        }
        if (isset($request->getQueryParams()['fail'])) {
            $this->flash->addMessage('error', 'Invalid operation !');
        }

        return $this->view->render($response, '/user/bus.twig', $data);
    })->setName('userBus');

    $app->post('/bus', function ($request, $response, $args) {
        $bus = new Bus();
        $uid = $this->session->id;
        $action = $request->getParsedBody()['delete'];
        if ((bool)$action) {
            $id = $request->getParsedBody()['id'];
            if ($bus->deleteReserve($id, $uid)) {
                return $response->withRedirect('/user/bus?success');
            } else {
                return $response->withRedirect('/user/bus?fail');
            }
        }
//        return $response->withRedirect('/user/bus?success');
    })->setName('deleteBus');

    $app->get('/package', function ($request, $response, $args) {
        $name = $this->session->name;
        $package = new Package();
        $packages = $package->readUserUnpickedPackage($name);
        $oldPackages = $package->readUserPickedPackage($name);
        $data = ['packages' => $packages, 'oldPackages' => $oldPackages];
        return $this->view->render($response, '/user/package.twig', $data);
    })->setName('userPackage');

    $app->post('/package', function ($request, $response, $args) {
        $name = $this->session->name;
        $package = new Package();
        $action = $request->getParsedBody()['sign'];
        if ((bool)$action) {
            $id = $request->getParsedBody()['id'];
            $package->signPackage($name, $id);
        }

        return $response->withRedirect('/user/package');
    });

    $app->get('/lostfound', function ($request, $response, $args) {
        $package = new Package();
        $packages = $package->lostFoundPackage();
        $data = ['packages' => $packages];
        return $this->view->render($response, '/user/lost.found.twig', $data);
    })->setName('lostFound');

});

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
            $student = new Student();
            $email = $student->fetch($uid)->getPrimaryMail();
            $mail = new Mail();
            $mail->busReserveConfirm($email);
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
    //Bus Section
    $app->group('/bus', function ($app) {
        $app->get('', function ($request, $response, $args) {
            $bus = new Bus();
            if (isset($request->getQueryParams()['edit']) && $request->getQueryParams()['edit'] == true) {
                $data = $request->getQueryParams();
                $bus->edit($data);
                return $response->withRedirect('/admin/bus?success');
            }
            if (isset($request->getQueryParams()['create']) && $request->getQueryParams()['create'] == true) {
                $data = $request->getQueryParams();
                $bus->adminCreate($data);
                return $response->withRedirect('/admin/bus?success');
            }
            if (isset($request->getQueryParams()['delete']) && $request->getQueryParams()['delete'] == true) {
                $id = $request->getQueryParams()['id'];
                $bus->delete($id);
                return $response->withRedirect('/admin/bus?success');
            }
            $users = [];
            if (isset($request->getQueryParams()['user'])) {
                $id = $request->getQueryParams()['user'];
                $users = $bus->showReserveUser($id);
            }
            if (isset($request->getQueryParams()['deluser'])) {
                $id = $request->getQueryParams()['deluser'];
                $users = $bus->deleteUser($id);
                $this->flash->addMessage('success', 'Operation Success !');
                $uri = $request->getQueryParams()['from'] . "&success";
                return $response->withRedirect($uri);
            }

            $schedule = ['schedules' => $bus->getSchedule(), 'users' => $users];
            if (isset($request->getQueryParams()['success'])) {
                $this->flash->addMessage('success', 'Operation Success !');
            }

            return $this->view->render($response, '/admin/bus.show.twig', $schedule);
        })->setName('busSchedule');

        $app->get('/suspended', function ($request, $response, $args) {
            $bus = new Bus();
            $userList = $bus->readSuspendList();
            if (isset($request->getQueryParams()['edit']) && $request->getQueryParams()['edit'] == true) {
                $data = $request->getQueryParams();
                $bus->updateSuspendList($data);
                return $response->withRedirect('/admin/bus/suspended?success');
            }
            if (isset($request->getQueryParams()['create']) && $request->getQueryParams()['create'] == true) {
                $data = $request->getQueryParams();
                $bus->createSuspendList($data);
                return $response->withRedirect('/admin/bus/suspended?success');
            }
            if (isset($request->getQueryParams()['delete']) && $request->getQueryParams()['delete'] == true) {
                $id = $request->getQueryParams()['id'];
                $bus->deleteSuspendList($id);
                return $response->withRedirect('/admin/bus/suspended?success');
            }

            $data = ['suspend_users' => $userList];

            if (isset($request->getQueryParams()['success'])) {
                $this->flash->addMessage('success', 'Operation Success !');
            }

            return $this->view->render($response, '/admin/bus.suspend.twig', $data);
        })->setName('busSuspend');
    });
    //Admin Section
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

$app->get('/test', function ($req, $res, $args) {
    // Set flash message for next request
    $this->flash->addMessage('error', 'This is a message');

    // Redirect
    return $res->withHeader('Location', '/');
});

//$app->get('/bar', function ($req, $res, $args) {
//    // Get flash messages from previous request
//    $messages = $this->flash->getMessages();
//    print_r($messages);
//
//    // Get the first message from a specific key
//    $test = $this->flash->getFirstMessage('error');
//    print_r($test);
//});