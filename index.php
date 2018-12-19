<?php
//include
include_once("inc/functions.php");
include_once('inc/config.php');
include_once('controllers/baseController.php');
include_once('model/baseModel.php');
include_once("model/postProcessor.class.php");
include("model/user.class.php");
include("model/database.class.php");

//vytvoření objektů
$login = new User;
$db = new Database;
$db->Connect();

$params = array();
$menu = array();

//twig
require_once __DIR__ . '/vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

//změna stránky
if (isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"];
} else {
    $page = "home";
}

//vyhození zablokovaného uživatele
$allBlocked = $db->blockedUsers();
$active = $login->getLogged();
if (($allBlocked != null) && ($active != null)) {
    foreach ($allBlocked as $blocked) {
        if ($blocked['login'] == $active['name']) {
            $login->logout();
        }
    }
}

//obsloužení formulářů
$post = new PostProcessor($_POST, $db, $login);
$messages = $post->doPost();

//menu
$menu["home"] = "Úvod";
if ($login->isLogged()) {
    $menu["members"] = "Členové";
}
$menu["posts"] = "Příspěvky";
$menu["faq"] = "FAQ";

//parametry
$params["menu"] = $menu;
$params["db"] = $db;

if (isset($messages['error'])) {
    $params['error'] = $messages['error'];
}
if (isset($messages['message'])) {
    $params['message'] = $messages['message'];
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
} else {
    $ctrl_name = "errorController";
    include_once("controllers/errorController.php");
}  
$$ctrl_name = new $ctrl_name($twig);
$$ctrl_name->indexAction($params);