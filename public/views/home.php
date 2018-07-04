<?php

include_once(__DIR__ . '/../../vendor/autoload.php');

use DebugBar\StandardDebugBar;

$debugbar = new StandardDebugBar();
$debugbarRenderer = $debugbar->getJavascriptRenderer();

$debugbar["messages"]->addMessage("hello world!");

include_once(__DIR__ . "/../inc/auth.php");
//include_once(__DIR__ . "/inc/header.php");

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


    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="">We've got what you need!</h2>
                    <hr class="light my-4">
                    <p class="mb-4">NCTU+是一個交大非官方的資訊組織，有鑑於目前校園系統仍有許多改善空間，我們從改寫校園系統開始，擴展出許多更便利、更友善且更美觀的服務。
                        <br>
                        我們一方面向校方請求開放Data及API，另一方面聆聽同學們的需求並不斷發想新的點子，我們不僅純粹的coding，我們希望結合行銷、設計、工程等不同領域的人才，不斷地進步使平台變的更好。
                    </p>
                    <a class="btn btn-light btn-xl js-scroll-trigger" href="#services">Get Started!</a>
                </div>
            </div>
        </div>
    </section>



    <footer class="">
        <div class="container text-center">
            <p>Copyright © TKUL❤Life 2018</p>
        </div>
    </footer>
</div>
<?php echo $debugbarRenderer->render() ?>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>

</body>
</html>
