<?php
//include
include_once("inc/functions.php");
include_once('inc/config.php');
include_once('controllers/baseController.php');
include_once('model/baseModel.php');
include("model/user.class.php");
include("model/database.class.php");

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
    
//uživatel    
    if ((isset($_POST["log"]))) {

    //login    
        if ($_POST["log"] == "login") {
            if ($db->authorizeUser($_POST["name"], $_POST["password"]) ) {
                $allBlocked = $db->blockedUsers();

                //není user zablokován?
                $blocked = false;
                if (($allBlocked != null)) {
                    foreach ($allBlocked as $blockedUser) {
                        if ($blockedUser['login'] == $_POST["name"]) {
                            echo $blockedUser['login'] .'=='. $_POST["name"];
                                $blocked = true;
                        }
                    }
                }
                
                if ($blocked == false) {
                    $login->login($_POST["name"]);
                    $logged = $login->getLogged();
                    $params["user"] = $db->getUser($_POST["name"]);
                    header("Location: /index.php?page=home");
                } else {
                    $params["error"] = "Váš účet byl zablokován";
                }
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
                $params["error"] = "Smazání uživatele se nezdařilo";
            } else {
                $params["message"] = "Uživatel byl smazán";
            }
            
        } else if ($_POST["log"] == "block") {   
            $db->blockUser($_POST["loginUser"]);
            $params["message"] = "Uživatel ".$_POST["loginUser"]." zablokován";
            
        } else if ($_POST["log"] == "unblock") {   
            $db->unblockUser($_POST["loginUser"]); 
            $params["message"] = "Uživatel ".$_POST["loginUser"]." odblokován";
     
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
            
    //změna jména        
        } else if ($_POST["log"] == "changeName") { 
            $log = $login->getLogged();
            $db->changeName($log['name'], $_POST["name"]);
            $params["message"] = "Změna jména provedena";
            
    //změna role        
        } else if ($_POST["log"] == "setRight") {   
            $res = $db->updateRight($_POST["login"], $_POST["right"]);
            $params["message"] = "Role změněna";
        }    
        

//příspěvek        
    } else  if ((isset($_POST["post"]))) {
    //nový příspěvek   
        if ($_POST["post"] == "newPost") {
            $author = $login->getLogged();
            
            $fileName = null;
            $ok = 0;
            if (isset($_FILES["file"])) {
                if ($_FILES["file"]["name"] != null) {
                    $fileType = strtolower(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION));

                    if ($fileType == "pdf") {
                        //velikost souboru
                        if ($_FILES["file"]["size"] > 500000) {
                            $fileError = "Nahraný soubor je moc velký, povolená velikost do 500kB";
                            $ok = 1;
                        } else {
                            //nahrání na server s novým jménem
                            $date = getdate();
                            $target_dir = "files/";
                            $target_file = $author["name"] . "_" . $date['yday'] . "-" . $date['year'] . "-" .
                                           $date['seconds']. "-" . $date['minutes'] . "-" . $date['hours'] .".pdf" ;
                
                            move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$target_file);
                        }
                    } else {
                        $fileError = "Nahraný soubor není ve správném formátu, akceptovány jsou pouze pdf soubory";
                        $ok = 1;
                    }
                }
            }
            
            if (isset($target_file)) {
                $fileName = $target_file;
            }
            if ($ok == 1) {
                $params["error"] = $fileError;
            }
            
            $res = $db->addPost($author["name"], $_POST["headline"], $_POST["content"], $fileName);
            if (!$res) {
                $params["error"] .= "Přidání příspěvku se nezdařilo";
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
            $fileName = null;
            $ok = 0;
            if (isset($_FILES["file"])) {
                if ($_FILES["file"]["name"] != null) {
                    $author = $login->getLogged();
                    $fileType = strtolower(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION));

                    if ($fileType == "pdf") {
                        //velikost souboru
                        if ($_FILES["file"]["size"] > 500000) {
                            $fileError = "Nahraný soubor je moc velký, povolená velikost do 500kB";
                            $ok = 1;
                        } else {
                            //nahrání na server s novým jménem
                            $date = getdate();
                            $target_dir = "files/";
                            $target_file = $author["name"] . "_" . $date['yday'] . "-" . $date['year'] . "-" .
                                           $date['seconds']. "-" . $date['minutes'] . "-" . $date['hours'] .".pdf" ;
                
                            move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$target_file);
                        }
                    } else {
                        $fileError = "Nahraný soubor není ve správném formátu, akceptovány jsou pouze pdf soubory";
                        $ok = 1;
                    }
                }
            }
             
            if (isset($target_file)) {
                $fileName = $target_file;
            }
            if ($ok == 1) {
                $params["error"] = $fileError;
            }
            
            $db->editPost($_POST["idPost"], $_POST["headline"], $_POST["content"], $fileName);
            $params["message"] = "Příspěvek byl upraven";
            
    //smazat příspěvek
        } else if ($_POST["post"] == "delete") {
            $recs = $db->getRecs($_POST["idPost"]);
            
            foreach ($recs as $rec) {
                $db->deleteRec($rec['id']);
            }
            
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
        
        
//recenze        
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
        
//soubor        
    } else  if ((isset($_POST["file"]))) {
    //smazat soubor   
        if ($_POST["file"] == "delete") {
            $db->deleteFile($_POST['idPost']);
            $params["message"] = "Soubor byl odstraněn";
        
            
    //změnit ikonku        
        } else if ($_POST["file"] == "icon") {
            $target_dir = "img/";
            $fileName = basename($_FILES["file"]["name"]);
            $fileType = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
            $ok = 1;
            
            // Check if image file is a actual image or fake image
            if(getimagesize($_FILES["file"]["tmp_name"]) == false) {
                $params["error"] = "Špatný formát obrázku";
                $ok = 0;
            }
            
            // Check file size
            if ($_FILES["file"]["size"] > 100000) {
                $params["error"] = "Jen soubory do velikosti 100kB jsou povoleny";
                $ok = 0;
            }
            
            // Allow certain file formats
            if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
                    && $fileType != "gif" ) {
                $params["error"] = "Špatný formát obrázku, povoleny: PNG, JPEG, GIF";
                $ok = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($ok == 1) {
                $date = getdate();
                $author = $login->getLogged();
                $fileName = $author["name"] . "_" . $date['yday'] . "-" . $date['year'] . "-" .
                               $date['seconds']. "-" . $date['minutes'] . "-" . $date['hours'] .$fileType ;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$fileName)) {
                    $params["message"] = "Obrázek byl nahrán";
                    
                    $db->changeIcon($author["name"], $fileName);
                } else {
                    $params["error"] = "Nahrání se nezdařilo";
                }
            }
        }
        
    //nic    
    } else {
        echo 'Nic se nestalo';
    }
}

$allBlocked = $db->blockedUsers();
$active = $login->getLogged();
if (($allBlocked != null) && ($active != null)) {
    foreach ($allBlocked as $blocked) {
        if ($blocked['login'] == $active['name']) {
            $login->logout();
        }
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