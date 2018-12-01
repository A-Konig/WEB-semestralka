<?php

//include
include_once("inc/functions.php");
include_once('inc/config.php');
include_once('controllers/baseController.php');
include("users/user.class.php");
include("users/database.class.php");

$login = new User;
$db = new Database;

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
$params["db"] = $db;

//obsloužení post
if (isset($_POST["submit"])) {
    
    if ((isset($_POST["log"]))) {

    //login    
        if ($_POST["log"] == "login") {
            if ($db->authorizeUser($_POST["name"], $_POST["password"]) ) {
                $login->login($_POST["name"]);
                $logged = $login->getLogged();
                $params["user"] = $db->getUser($_POST["name"]);
                $page = "home";
            } else {
                echo "<script type='text/javascript'>alert('Špatné heslo nebo uživatelské jméno');</script>";
            }
        } else if ($_POST["log"] == "logout") {
            $login->logout();

    //registrace
        } else if ($_POST["log"] == "register") {
            $res = $db->createUser($_POST["name"], $_POST["login"], $_POST["password"], $_POST["email"], 2);
            if ($res) {
                $login->login($_POST["login"]);
                $logged = $login->getLogged();
                $params["user"] = $db->getUser($_POST["login"]);
                $page = "home";
            }
        }
    } else {
        echo 'Nic se nestalo';
    }
}

if (!$login->isLogged()) {
    $params["user"] = null;
} else {
    $logged = $login->getLogged();
    $loggedData = $db->getUser($logged['name']);
    $params["user"] = $loggedData;
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