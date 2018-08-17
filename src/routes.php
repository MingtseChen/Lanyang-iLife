<?php
//TODO : Add Controller

use Respect\Validation\Validator as v;

include 'Models/StudentModel.php';
include 'Models/UserModel.php';
include 'Models/BusModel.php';
include 'Models/PackageModel.php';
include 'Models/RepairModel.php';
include 'Plugins/Mail.php';
include 'Plugins/Upload.php';

//include 'Middleware/AdminSection.php';
//include 'Middleware/RedirectIfAuth.php';

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
    return $this->view->render($response, 'home.twig', $data);
})->setName('home');

//User
$app->group('/user', function ($app) {

    $app->get('/index', function ($request, $response, $args) {
        $id = $this->session->id;
        $student = new Student();
        $user = $student->fetch($id);
        $mail = $user->getMail();
        $mail2 = $user->getSecondaryMail();
        $data = ["email" => $mail, 'email2' => $mail2];
        return $this->view->render($response, '/user/index.twig', $data);
    })->setName('userIndex');

    $app->post('/index', function ($request, $response, $args) {
        $email = $request->getParsedBody()['mail'];
        $student = new Student();
        $id = $this->session->id;
        $student->addSecondaryMail($id, $email);
        $this->flash->addMessage('success', 'Info updated');
        return $response->withRedirect('/user/index');
    })->setName('userAddMail');

    $app->get('/bus', function ($request, $response, $args) {
        $bus = new Bus();
        $id = $this->session->id;
        $buses = $bus->readUserBus($id);
        $data = ['buses' => $buses];
        return $this->view->render($response, '/user/bus.twig', $data);
    })->setName('userBus');

    $app->post('/bus', function ($request, $response, $args) {
        $bus = new Bus();
        $uid = $this->session->id;
        $action = $request->getParsedBody()['delete'];
        if ((bool)$action) {
            $id = $request->getParsedBody()['id'];
            $status = $bus->deleteReserve($id, $uid);
            if ($status) {
                $this->flash->addMessage('success', 'operation success !');
            } else {
                $this->flash->addMessage('error', 'Invalid operation !');
            }
        }
        return $response->withRedirect('/user/bus');
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
            $status = $package->signPackage($name, $id);
            if ($status) {
                $this->flash->addMessage('success', 'operation success !');
            } else {
                $this->flash->addMessage('error', 'Invalid operation !');
            }
        }

        return $response->withRedirect('/user/package');
    });

    $app->get('/lostfound', function ($request, $response, $args) {
        $package = new Package();
        $packages = $package->lostFoundPackageRead();
        $data = ['packages' => $packages];
        return $this->view->render($response, '/user/lost.found.twig', $data);
    })->setName('lostFound');

});

//iBus
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

//Repair
$app->group('/repair', function ($app) {
    $app->get('', function ($request, $response, $args) {
        return $this->view->render($response, '/repair/index.twig');
    })->setName('repair');;
    $app->get('/create', function ($request, $response, $args) {
        $repair = new Repair();
        $category = $repair->readCategory();
        $buildings = $repair->readBuilding();
        $data = ['categories' => $category, 'buildings' => $buildings];
        return $this->view->render($response, '/repair/create.twig', $data);
    })->setName('repairCreate');
    $app->post('/create', function ($request, $response, $args) {
        //TODO handle upload exceeds server's threshhold cause blank form send and 500 response
        $repair = new Repair();
        $upload = new FileUpload();
        $params = $request->getParsedBody();
        $filename = '';

        $uid = $this->session->id;
        $building = $params['building'];
        $room = $params['room'];
        $item_cat = $params['item_cat'];
        $item = $params['item'];
        $desc = $params['desc'];
        $accompany = $params['accompany'];
        $confirm = isset($params['confirm']) ? true : false;
        //file upload

        if ($_FILES['file']['size'] > 0 && $_FILES['file']['error'] == 0) {
            $path = $this->get('repair_storage');
            $uploadStatus = $upload->filePath('file', $path)->repairImageUpdate();
            if ($uploadStatus['status']) {
                $filename = $uploadStatus['file_name'];
            } else {
                $this->flash->addMessage('error', $uploadStatus['info']);
                $uri = $request->getUri();
                return $response->withRedirect($uri->getPath());
            }
        }
        //pass form params
        $status = $repair->createRepair($uid, $building, $room, $item_cat, $item, $desc, $accompany, $confirm,
            $filename);
        if ($status) {
            $this->flash->addMessage('success', 'form submitted');
            return $response->withRedirect('/');
        } else {
            $this->flash->addMessage('error', 'invalid operation');
            $uri = $request->getUri();
            return $response->withRedirect($uri->getPath());
        }
    })->setName('repairSubmit');
});

//Login
$app->group('/login', function ($app) {

    $app->get('', function ($request, $response, $args) {
        return $this->view->render($response, '/auth/login.twig');
    })->setName('loginForm');

    $app->get('/recover', function ($request, $response, $args) {
        return $this->view->render($response, '/auth/forget.twig');
    })->setName('recover');

});

//Logout
$app->get('/logout', function ($request, $response, $args) {
    $this->session::destroy();
    return $response->withRedirect('/');
})->setName('logout');

//Admin
$app->group('/admin', function ($app) {
    $app->get('', function ($request, $response, $args) {
        return $this->view->render($response, '/admin/index.twig');
    })->setName('admin');
    //Package session
    $app->group('/package', function ($app) {

        $app->get('', function ($request, $response, $args) {
            $package = new Package();
            $allPackage = $package->readAllUnpickPackage();
            $data = ['packages' => $allPackage];
            return $this->view->render($response, '/admin/package.show.twig', $data);
        })->setName('packageIndex');

        $app->post('/edit', function ($request, $response, $args) {
            // Get header from request object
            $path = '';
            $refererHeader = $request->getHeader('HTTP_REFERER');
            if ($refererHeader) {
                // Extract referer value
                $uri = array_shift($refererHeader);
                $path = parse_url($uri);
            }
            $data = $request->getParsedBody();
            $package = new Package();
            $status = $package->updatePackage($data['id'], $data['rcp'], $data['cat'], $data['strg'], $data['pid'],
                $data['time']);
            if ($status) {
                $this->flash->addMessage('success', 'Package updated');
            } else {
                $this->flash->addMessage('error', 'invalid');
            }
            return $response->withRedirect($path['path']);
        })->setName('packageUpdate');

        $app->post('/create', function ($request, $response, $args) {
            $mail = new Mail();
            $student = new Student();

            $data = $request->getParsedBody();
            $package = new Package();
            $status = $package->createPackage($data['rcp'], $data['cat'], $data['strg'], $data['pid'], $data['time']);
            if ($status) {
                $uid = $this->session->id;
                $email = $student->fetch($uid)->getPrimaryMail();

                $mail->packageConfirm($email);
                $this->flash->addMessage('success', 'Package updated');
            } else {
                $this->flash->addMessage('error', 'Fail to insert data');
            }
            return $response->withRedirect('/admin/package');
        })->setName('packageCreate');

        $app->post('/delete', function ($request, $response, $args) {
            // Get header from request object
            $path = '';
            $refererHeader = $request->getHeader('HTTP_REFERER');
            if ($refererHeader) {
                // Extract referer value
                $uri = array_shift($refererHeader);
                $path = parse_url($uri);
            }
            $data = $request->getParsedBody();
            $package = new Package();
            $status = $package->deletePackage($data['id']);
            if ($status) {
                $this->flash->addMessage('success', 'Package updated');
            } else {
                $this->flash->addMessage('error', 'Fail to insert data');
            }
            return $response->withRedirect($path['path']);
        })->setName('packageDelete');

        $app->post('/sign', function ($request, $response, $args) {
            $data = $request->getParsedBody();
            $package = new Package();
            $status = $package->signPackage($data['name'], $data['id']);
            if ($status) {
                $this->flash->addMessage('success', 'Package updated');
            } else {
                $this->flash->addMessage('error', 'Fail to insert data');
            }
            return $response->withRedirect('/admin/package');
        })->setName('packageSign');

        $app->post('/unsign', function ($request, $response, $args) {
            $data = $request->getParsedBody();
            $package = new Package();
            $status = $package->unsignPackage($data['name'], $data['id']);
            if ($status) {
                $this->flash->addMessage('success', 'Package updated');
            } else {
                $this->flash->addMessage('error', 'Fail to insert data');
            }
            return $response->withRedirect('/admin/package/history');
        })->setName('packageUnsign');

        $app->post('/lost', function ($request, $response, $args) {
            $data = $request->getParsedBody();
            $package = new Package();
            if (isset($data['cancel']) && (bool)$data['cancel'] == true) {
                $status = $package->lostFoundUndoPackage($data['id']);
            } else {
                $status = $package->lostFoundPackage($data['id']);
            }

            if ($status) {
                $this->flash->addMessage('success', 'Package updated');
            } else {
                $this->flash->addMessage('error', 'Fail to insert data');
            }
            return $response->withRedirect('/admin/package/history');
        })->setName('packageLost');

        $app->get('/history', function ($request, $response, $args) {
            $package = new Package();
            $allPackage = $package->readHistoryPackage();
            $data = ['packages' => $allPackage];
            return $this->view->render($response, '/admin/package.history.twig', $data);
        })->setName('packageHistory');


    });
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
        //Update user
        $app->post('/update', function ($request, $response, $args) {
            $user = new User();
            $data = $request->getParsedBody();
            $id = $data['id'];
            $role = $data['role'];
            $active = $data['active'];
            $status = $user->updateAdmin($id, $active, $role);
            if ($status) {
                $this->flash->addMessage('success', 'delete success');
            } else {
                $this->flash->addMessage('error', 'invalid');
            }
            return $response->withRedirect('/admin/users');
        })->setNAme('userUpdate');
        //Create user
//        $app->post('/create', function ($request, $response, $args) {
//        })->setName('');
        //Delete user
        $app->post('/delete', function ($request, $response, $args) {
            $user = new User();
            $data = $request->getParsedBody();
            $status = $user->delete($data['id']);
            if ($status) {
                $this->flash->addMessage('success', 'delete success');
            } else {
                $this->flash->addMessage('error', 'invalid');
            }
            return $response->withRedirect('/admin/users');
        })->setName('userDelete');

        //Validate Rules
        $uid = v::digit()->noWhitespace()->length(1, 10);
        $validators = array(
            'uid' => $uid,
        );
        $app->post('/create', function ($request, $response, $args) {
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
        })->setName('submitUser')->add(new \DavidePastore\Slim\Validation\Validation($validators));
    });
    //Repair Section
    $app->group('/repair', function ($app) {
        $app->get('', function ($request, $response, $args) {
            $uid = $this->session->id;
            $item = new Repair();
            //for status please check at 'repair_status' table
            $itemStatus0 = $item->readWork($uid, 0);
            $itemStatus1 = $item->readWork($uid, 1);
            $itemStatus2 = $item->readWork($uid, 2);
            $itemStatus3 = $item->readWork($uid, 3);
            $itemStatus4 = $item->readWork($uid, 4);
            $data = [
                'tab1' => $itemStatus0,
                'tab2' => array_merge($itemStatus1, $itemStatus2),
                'tab3' => $itemStatus3,
                'tab4' => $itemStatus4,
            ];
            return $this->view->render($response, '/admin/repair.work.twig', $data);
        })->setName('repairWork');

        $app->get('/sign/{id}', function ($request, $response, $args) {
            $item = new Repair();
            $id = $args['id'];
            $detail = $item->showRepairDetail($id);
            if (!$detail) {
                $this->flash->addMessage('error', 'invalid ');
                return $response->withRedirect('/admin/repair');
            }
            $data = [
                'item' => $detail,
                'cats' => $item->readCategory(),
                'buildings' => $item->readBuilding()
            ];

            return $this->view->render($response, '/admin/repair.sign.twig', $data);
        })->setName('repairSign');

        $app->get('/dispatch/{id}', function ($request, $response, $args) {
            $item = new Repair();
            $id = $args['id'];
            $detail = $item->showRepairDetail($id);
            if (!$detail) {
                $this->flash->addMessage('error', 'invalid ');
                return $response->withRedirect('/admin/repair');
            }
            $data = [
                'item' => $detail,
                'cats' => $item->readCategory(),
                'buildings' => $item->readBuilding()
            ];

            return $this->view->render($response, '/admin/repair.dispatch.twig', $data);
        })->setName('repairDispatch');

        $app->get('/finish/{id}', function ($request, $response, $args) {
            $item = new Repair();
            $id = $args['id'];
            $detail = $item->showRepairDetail($id);
            if (!$detail) {
                $this->flash->addMessage('error', 'invalid ');
                return $response->withRedirect('/admin/repair');
            }
            $data = [
                'item' => $detail,
                'cats' => $item->readCategory(),
                'buildings' => $item->readBuilding()
            ];

            return $this->view->render($response, '/admin/repair.finish.twig', $data);
        })->setName('repairFinish');

        $app->post('/actions', function ($request, $response, $args) {
            $item = new Repair();
            $action = $request->getParsedBody()['action'];

            //sign
            if ($action == 'sign') {
                $id = $request->getParsedBody()['id'];
                $status = $item->signWork($id);
                if ($status) {
                    return 'success';
                } else {
                    return 'error';
                }
            }
            //edit
            if ($action == 'edit') {
                $building = $request->getParsedBody()['building'];
                $cat = $request->getParsedBody()['item_cat'];
                $id = $request->getParsedBody()['id'];
                $status = $item->edit($id, $building, $cat);
                if ($status) {
                    $this->flash->addMessage('success', 'data saves ');
                    return $response->withRedirect('/admin/repair');
                } else {
                    $this->flash->addMessage('error', 'invalid ');
                    return $response->withRedirect('/admin/repair');
                }
            }
            //dispatch
            if ($action == 'dispatch') {
                $id = $request->getParsedBody()['id'];
                $assign = $request->getParsedBody()['assign'];
                $assign_notes = $request->getParsedBody()['assign_notes'];
                $repair_man = $request->getParsedBody()['repair_man'];
                $expect_mod = $request->getParsedBody()['expect_mod'];

                $status = $item->dispatchItem($id, $assign, $assign_notes, $repair_man, $expect_mod);
                if ($status) {
                    $this->flash->addMessage('success', 'data saves');
                    return $response->withRedirect('/admin/repair');
                } else {
                    $this->flash->addMessage('error', 'invalid ');
                    return $response->withRedirect('/admin/repair');
                }
            }
            //finish
            if ($action == 'finish') {
                $id = $request->getParsedBody()['id'];
                $repair_notes = $request->getParsedBody()['repair_notes'];
                $status = $item->finishItem($id, $repair_notes);
                if ($status) {
                    $this->flash->addMessage('success', 'data saves ');
                    return $response->withRedirect('/admin/repair');
                } else {
                    $this->flash->addMessage('error', 'invalid ');
                    return $response->withRedirect('/admin/repair');
                }
            }
        })->setName('repairAction');
    });
});

//Console
$app->post('/console', 'RunTracy\Controllers\RunTracyConsole:index');