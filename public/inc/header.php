<?php
$authUser = new Auth;
$auth_status = $authUser->isLogin();
$debugbar["messages"]->addMessage("Login Status : $auth_status");
//$debugbar["messages"]->addMessage($authUser->isLogin());
?>
<!doctype html>
<html lang="en">

<head>
    <script
            src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous"></script>
    <?php echo $debugbarRenderer->renderHead() ?>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Hello, world!</title>
    <style>
        header.masthead {
            padding-top: 10rem;
            padding-bottom: calc(10rem - 56px);
            background-image: url("assets/bg.jpg");
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            background-position-x: center;
            background-position-y: center;
            height: 100vh;
            min-height: 650px;
        }

        section {
            padding: 8rem 0;
        }

        header.masthead-login {
            /*padding-top: 10rem;*/
            /*padding-bottom: calc(10rem - 56px);*/
            background-image: url("../../assets/cover.jpg");
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            background-position-x: center;
            background-position-y: center;
            height: 100vh;
            /*min-height: 650px;*/
        }
    </style>
</head>

<body>
<div>
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-transparent ">
        <div class="container">
            <a class="navbar-brand" href="#">iLife</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">交通車預約</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">大三出國通報</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">線上報修</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">蘭陽掛包</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    <?php if (!$auth_status) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="http://sso.tku.edu.tw/ilifelytest/public/">登入</a>
                        </li>
                    <?php } ?>
                    <?php if ($auth_status) { ?>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo $authUser->sso_userid() ?><span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="#" method="POST" style="display: none;">
                                </form>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    <header class="masthead">
    </header>
