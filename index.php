<?php

//include
include_once("inc/functions.php");
include_once('inc/config.php');
include_once('controllers/baseController.php');
include_once('users/baseModel.php');
include("users/user.class.php");
include("users/database.class.php");

$login = new User;
$db = new Database;
$db->Connect();

$params = array();
$menu = array();
$pg = array();

//twig
require_once __DIR__ . '/vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('sablony');
$twig = new Twig_Environment($loader);

//změna stránky
if (isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"];
} else {
    $page = "home";
}

//obsloužení formulářů
if (isset($_POST["submit"])) {
    
    if ((isset($_POST["log"]))) {

    //login    
        if ($_POST["log"] == "login") {
            if ($db->authorizeUser($_POST["name"], $_POST["password"]) ) {
                $login->login($_POST["name"]);
                $logged = $login->getLogged();
                $params["user"] = $db->getUser($_POST["name"]);
               header("Location: /index.php?page=home");
            } else {
                $params["error"] = "Přihlášení se nezdařilo";
            }
        } else if ($_POST["log"] == "logout") {
            $login->logout();
            $page = "logoutPage";

    //registrace
        } else if ($_POST["log"] == "register") {
            $res = $db->createUser($_POST["name"], $_POST["login"], $_POST["password"], $_POST["email"], 3);
            if ($res) {
                $login->login($_POST["login"]);
                $logged = $login->getLogged();
                $params["user"] = $db->getUser($_POST["login"]);
                $page = "home";
            } else {
                $params["error"] = "Uživatel se stejným přihlašovacím jménem již existuje";
            }
        
        
    //smazání uživatele
        } else if ($_POST["log"] == "delete") {   
            $res = $db->deleteUser($_POST["loginUser"]);
            if (!$res) {
                $params["error"] = "Smazání příspěvku se nezdařilo";
            } else {
                $params["message"] = "Uživatel byl smazán";
            }
     
    //změna e-mailu    
        } else if ($_POST["log"] == "changeMail") { 
            $user = $login->getLogged();
            if ($db->authorizeUser($user['name'], $_POST["password"]) ) {
                $db->updateEmail($user['name'], $_POST['email']);
                $params["message"] = "Změna e-mailu provedena";
            } else {
                $params["error"] = "Špatné heslo";
            }
        
    //změna hesla        
        } else if ($_POST["log"] == "changePass") {   
            $user = $login->getLogged();
            if ($db->authorizeUser($user['name'], $_POST["pass1"]) ) {
                $db->updatePassword($user['name'], $_POST['pass2']);
                $params["message"] = "Změna hesla provedena";
            } else {
                $params["error"] = "Špatné heslo";
            }
            
    //změna role        
        } else if ($_POST["log"] == "setRight") {   
            $res = $db->updateRight($_POST["login"], $_POST["right"]);
            $params["message"] = "Role změněna";
        }    
        
    } else  if ((isset($_POST["post"]))) {
    //nový příspěvek   
        if ($_POST["post"] == "newPost") {
            $author = $login->getLogged();
            
            $res = $db->addPost($author["name"], $_POST["headline"], $_POST["content"], $_POST["tags"]);
            if (!$res) {
                $params["error"] = "Přidání příspěvku se nezdařilo";
            } else {
                $params["message"] = "Příspěvek byl přidán";
            }
    
    //schválit příspěvek
        } else if ($_POST["post"] == "publish") {
            $db->publishPost($_POST["idPost"]);
            $published = $db->getPost($_POST["idPost"]);
            if (($published != null) && ($published['schvaleny'] ==  1) ) {
                $params["message"] = "Příspěvek byl publikován";
            } else {
                $params["error"] = "Publikování příspěvku se nezdařilo";
            }

    //upravit příspěvek        
        } else if ($_POST["post"] == "edit") {
           $db->editPost($_POST["idPost"], $_POST["headline"], $_POST["content"], $_POST["tags"]);
           $params["message"] = "Příspěvek byl upraven";
            
    //smazat příspěvek
        } else if ($_POST["post"] == "delete") {
            $recs = $db->getRecs($_POST["idPost"]);
            
            $res = $db->deletePost($_POST["idPost"]);
            if (!$res) {
                $params["error"] = "Smazání příspěvku se nezdařilo";
            } else {
                $params["message"] = "Příspěvek byl smazán";
            }
        
    //přiřazení recenzenta
        } else if ($_POST["post"] == "setRec") {
            $user = $db->getUser($_POST['loginRec']);
            $db->updateRec($user['login'], $_POST['idPost'], $_POST['numberRec']);
            
            $params["message"] = "Recenzent byl přiřazen";
        }  
    } else  if ((isset($_POST["rec"]))) {
    //nová recenze
        if ($_POST["rec"] == "newRec") {
           $author = $login->getLogged();
           $res = $db->addRec($author['name'], $_POST["content"], $_POST["lang"], $_POST["orig"], $_POST["summary"],  $_POST["idPost"]);
           
           //$db->updateRec(null, $_POST["idPost"]);
           
           if ($res) {
               $params["message"] = "Recenze byla publikována";
           } else {
               $params["error"] = "Došlo k chybě při přidávání recenze";
           }
        
    //smazat  
        } else if ($_POST["rec"] == "delete") {
           $res = $db->deleteRec($_POST["idRec"]);
           if (!$res) {
               $params["error"] = "Smazání recenze se nezdařilo";
           } else {
               $params["message"] = "Recenze byla smazána";
           }

    //edit       
        } else if ($_POST["rec"] == "edit") {
           $db->editRec($_POST["idRec"], $_POST["content"], $_POST["summary"], $_POST["lang"], $_POST["orig"]);
           $params["message"] = "Recenze byla upravena";
           
    //publikovat       
        } else if ($_POST["rec"] == "publish") {
            $db->publishRec($_POST["idRec"]);
            $published = $db->getOneRec($_POST["idRec"]);
            if (($published != null) && ($published['schvaleny'] ==  1) ) {
                $params["message"] = "Recenze byla publikována";
            } else {
                $params["error"] = "Publikování recenze se nezdařilo";
            }
        } 
        
    //nic    
    } else {
        echo 'Nic se nestalo';
    }
}

//menu
$menu["home"] = "Úvod";
if ($login->isLogged()) {
    $menu["members"] = "Členové";
} else {
    $pg["members"] = "Členové";
}
$menu["posts"] = "Příspěvky";
$menu["faq"] = "FAQ";

//stránky mimo menu
$pg["login"] = "Login";
$pg["register"] = "Registrovat";
$pg["newPost"] = "Nový příspěvek";
$pg["terms"] = "Podmínky";
$pg["logoutPage"] = "Odhlášení";
$pg["settings"] = "Nastavení";
$pg["viewPost"] = "Detail příspěvku";
$pg["myRec"] = "Moje recenze";
$pg["toPublish"] = "Ke schválení";

//vybraná stránka
if ((array_key_exists($page, $menu)) || (array_key_exists($page, $pg))) {
    $filename = $page . ".php";
} else {
    $filename = "error.php";
}

//parametry
$params["menu"] = $menu;
$params["db"] = $db;


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