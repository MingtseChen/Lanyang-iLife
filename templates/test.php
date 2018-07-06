<?php
include_once("inc/auth.php") ;
include_once("inc/database.php");

$auth = new Auth;
echo $auth->sso_userid();
echo $auth->sso_roletype();

//session_destroy();

