<?php

//include
include_once("inc/functions.php");
include_once('controllers/baseController.php');
include("users/user.class.php");
$login = new User;

//twig
require_once __DIR__ . '/vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('sablony');
$twig = new Twig_Environment($loader);

//nacte stranku z url
if (isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"];
} else {
    $page = "home";
}

//menu
$menu = array();
$menu["home"] = "Úvod";
//$menu["contacts"] = "Kontakt";
$menu["members"] = "Členové";
$menu["posts"] = "Příspěvky";
$menu["faq"] = "FAQ";

//stránky mimo menu
$pg = array();
$pg["login"] = "Login";
$pg["register"] = "Registrovat";
$pg["newPost"] = "Nový příspěvek";
$pg["terms"] = "Podmínky";

//vybraná stránka
if ((array_key_exists($page, $menu)) || (array_key_exists($page, $pg))) {
    $filename = $page . ".php";
} else {
    $filename = "error.php";
}

//parametry
$params = array();
$params["menu"] = $menu;

//obsloužení post
if (isset($_POST["submit"])) {
    
    if ((isset($_POST["log"]))) {
 
        if ($_POST["log"] == "login") {
            if (($_POST["name"] == "") || ($_POST["password"] == "")) {
                echo "<script type='text/javascript'>alert('Nevyplněné heslo nebo uživatelské jméno');</script>";
            } else {
                $login->login($_POST["name"]);
                $logged = $login->getLogged();
                $params["user"] = $logged;
                $page = "home";
            }

            } else if ($_POST["log"] == "logout") {
            $login->logout();

            } else if ($_POST["log"] == "register") {
            echo 'new user';
            }

    } else {
        echo 'Nic se nestalo';
    }
}

if (!$login->isLogged()) {
    $params["user"] = array();
} else {
    $logged = $login->getLogged();
    $params["user"] = $logged;
}

//výběr kontroleru
$ctrl_name = $page . "Controller";
$filename_ctrl = "controllers/$ctrl_name.php";

if (file_exists($filename_ctrl) && !is_dir($filename_ctrl)) {
    include_once($filename_ctrl);
    $$ctrl_name = new $ctrl_name($twig);
    $$ctrl_name->indexAction($params);
} else {
    $filename_ctrl = "controllers/errorController.php";
    include_once($filename_ctrl);
    $errorController = new errorController($twig);
    $errorController->indexAction($params);
}