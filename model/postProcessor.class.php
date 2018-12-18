<?php

/*
 * Třída obsahující metody obsluhující $_POST
 * 
 */
class PostProcessor {
    
    private $post;
    private $db;
    private $login;
    
    /**
     * Konstruktor třídy
     * 
     * @param type $post    $_POST
     * @param type $db      přístup k databázi (třída Database)
     * @param type $login   přihlášený uživatel (třída User)
     */
    public function __construct($post, $db, $login) {
        $this->post = $post;
        $this->db = $db;
        $this->login = $login;
    }

    /**
     * Základní metoda zpracující $_POST
     * 
     * @return type array obsahující výsledky zpracování: "message" => "zpráva o úspěchu", "error" => "zpráva o neúspěchu"
     */
    function doPost() {

        if (isset($this->post["submit"])) {

            //uživatel    
            if ((isset($this->post["log"]))) {
                return $this->doUser($this->post, $this->db, $this->login);

            //příspěvek        
            } else if ((isset($this->post["post"]))) {
                return $this->doArticle($this->post, $this->db, $this->login);

            //recenze        
            } else if ((isset($this->post["rec"]))) {
                return $this->doRec($this->post, $this->db, $this->login);

            //soubor        
            } else if ((isset($this->post["file"]))) {
                return $this->doFile($this->post, $this->db, $this->login);

            //nic    
            } else {
                echo 'Nic se nestalo';
            }
        }
    }

    /**
     * Metoda, obsluhující formuláře, související s uživatelem
     * 
     * @param type $post
     * @param type $db
     * @param type $login
     * @return array
     */
    private function doUser($post, $db, $login) {
        $params = array();
        //login    
        if ($post["log"] == "login") {
            if ($db->authorizeUser($post["name"], $post["password"])) {
                $toLog = $db->getUser($post["name"]);

                //není user zablokován?
                $blocked = false;
                if ($toLog['block'] == 1) {
                    $blocked = true;
                }

                if ($blocked == false) {
                    $login->login($post["name"]);
                    $logged = $login->getLogged();
                    $params["user"] = $db->getUser($post["name"]);
                    header("Location: /index.php?page=home");
                } else {
                    $params["error"] = "Váš účet byl zablokován";
                }
            } else {
                $params["error"] = "Přihlášení se nezdařilo";
            }
            return $params;
        } else if ($post["log"] == "logout") {
            $login->logout();
            header("Location: /index.php?page=logoutPage");
            return $params;

            //registrace
        } else if ($post["log"] == "register") {
            $res = $db->createUser($post["name"], $post["login"], $post["password"], $post["email"], 3);
            if ($res) {
                $login->login($post["login"]);
                $logged = $login->getLogged();
                $params["user"] = $db->getUser($post["login"]);
                header("Location: /index.php?page=home");
            } else {
                $params["error"] = "Registrace se nezdařila";
            }
            return $params;

            //smazání uživatele
        } else if ($post["log"] == "delete") {
            $res = $db->deleteUser($post["loginUser"]);
            if (!$res) {
                $params["error"] = "Smazání uživatele se nezdařilo";
            } else {
                $params["message"] = "Uživatel byl smazán";
            }
            return $params;
            //zablokování
        } else if ($post["log"] == "block") {
            $db->blockUser($post["loginUser"]);
            $params["message"] = "Uživatel " . $post["loginUser"] . " zablokován";
            return $params;

            //odblokování
        } else if ($post["log"] == "unblock") {
            $db->unblockUser($post["loginUser"]);
            $params["message"] = "Uživatel " . $post["loginUser"] . " odblokován";
            return $params;

            //změna e-mailu    
        } else if ($post["log"] == "changeMail") {
            $user = $login->getLogged();
            if ($db->authorizeUser($user['name'], $post["password"])) {
                $res = $db->updateEmail($user['name'], $post['email']);
                if ($res) {
                    $params["message"] = "Změna e-mailu provedena";
                } else {
                    $params["error"] = "Změna e-mailu se nezdařila";
                }
            } else {
                $params["error"] = "Špatně zadané heslo";
            }
            return $params;

            //změna hesla        
        } else if ($post["log"] == "changePass") {
            $user = $login->getLogged();
            if ($db->authorizeUser($user['name'], $post["pass1"])) {
                $db->updatePassword($user['name'], $post['pass2']);
                $params["message"] = "Změna hesla provedena";
            } else {
                $params["error"] = "Špatné heslo";
            }
            return $params;

            //změna jména        
        } else if ($post["log"] == "changeName") {
            $log = $login->getLogged();
            $res = $db->changeName($log['name'], $post["name"]);
            if ($res) {
                $params["message"] = "Změna jména provedena";
            } else {
                $params["error"] = "Změna jména se nezdařila";
            }
            return $params;

            //změna role        
        } else if ($post["log"] == "setRight") {
            $res = $db->updateRight($post["login"], $post["right"]);
            if ($res == true) {
                $params["message"] = "Role uživatele " . $post["login"] . " změněna";
            } else {
                $params["error"] = "Role se nepodařila změnit";
            }
            return $params;
        }
    }

     /**
     * Metoda, obsluhující formuláře, související s příspěvky
     * 
     * @param type $post
     * @param type $db
     * @param type $login
     * @return array
     */
    private function doArticle($post, $db, $login) {
        $params = array();
        //nový příspěvek   
        if ($post["post"] == "newPost") {
            $author = $login->getLogged();
            $params["error"] = null;

            $fileName = null;
            $ok = 0;
            if (isset($_FILES["file"])) {
                if ($_FILES["file"]["name"] != null) {
                    $fileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

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
                                    $date['seconds'] . "-" . $date['minutes'] . "-" . $date['hours'] . ".pdf";

                            move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $target_file);
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

            $res = $db->addPost($author["name"], $post["headline"], $post["content"], $fileName);
            if (!$res) {
                $params["error"] .= "Přidání příspěvku se nezdařilo";
            } else {
                $params["message"] = "Příspěvek byl přidán";
            }
            return $params;

            //schválit příspěvek
        } else if ($post["post"] == "publish") {
            $res = $db->publishPost($post["idPost"]);

            if ($res == true) {
                $params["message"] = "Příspěvek byl publikován";
            } else {
                $params["error"] = "Publikování příspěvku se nezdařilo";
            }
            return $params;

            //upravit příspěvek        
        } else if ($post["post"] == "edit") {
            $fileName = null;
            $ok = 0;
            if (isset($_FILES["file"])) {
                if ($_FILES["file"]["name"] != null) {
                    $author = $login->getLogged();
                    $fileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

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
                                    $date['seconds'] . "-" . $date['minutes'] . "-" . $date['hours'] . ".pdf";

                            move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $target_file);
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

            $res = $db->editPost($post["idPost"], $post["headline"], $post["content"], $fileName);
            if ($res == true) {
                $params["message"] = "Příspěvek byl upraven";
            } else {
                $params["error"] = "Editace příspěvku se nezdařila";
            }
            return $params;

            //smazat příspěvek
        } else if ($post["post"] == "delete") {
            $recs = $db->getRecs($post["idPost"]);

            foreach ($recs as $rec) {
                $db->deleteRec($rec['id']);
            }

            $res = $db->deletePost($post["idPost"]);
            if (!$res) {
                $params["error"] = "Smazání příspěvku se nezdařilo";
            } else {
                $params["message"] = "Příspěvek byl smazán";
            }
            return $params;

            //zamítnout příspěvek
        } else if ($post["post"] == "deny") {
            $res = $db->denyPost($post["idPost"]);
            $recs = $db->getRecs($post["idPost"]);

            foreach ($recs as $rec) {
                $db->deleteRec($rec['id']);
            }
            if (!$res) {
                $params["error"] = "Zamítnutí příspěvku se nezdařilo";
            } else {
                $params["message"] = "Příspěvek byl zamítnut";
            }
            return $params;

            //přiřazení recenzenta
        } else if ($post["post"] == "setRec") {
            $user = $db->getUser($post['loginRec']);
            $db->updateRec($user['login'], $post['idPost'], $post['numberRec']);

            $params["message"] = "Recenzent byl přiřazen";
            return $params;
        }
    }

     /**
     * Metoda, obsluhující formuláře, související s recenzemi.
     * 
     * @param type $post
     * @param type $db
     * @param type $login
     * @return array
     */
    private function doRec($post, $db, $login) {
        $params = array();
        //nová recenze
        if ($post["rec"] == "newRec") {
            $author = $login->getLogged();
            $res = $db->addRec($author['name'], $post["content"], $post["lang"], $post["orig"], $post["summary"], $post["idPost"]);

            if ($res) {
                $params["message"] = "Recenze byla publikována";
            } else {
                $params["error"] = "Došlo k chybě při přidávání recenze";
            }
            return $params;

            //smazat  
        } else if ($post["rec"] == "delete") {
            $res = $db->deleteRec($post["idRec"]);
            if (!$res) {
                $params["error"] = "Smazání recenze se nezdařilo";
            } else {
                $params["message"] = "Recenze byla smazána";
            }
            return $params;

            //edit       
        } else if ($post["rec"] == "edit") {
            $res = $db->editRec($post["idRec"], $post["content"], $post["summary"], $post["lang"], $post["orig"]);
            if ($res) {
                $params["message"] = "Recenze byla upravena";
            } else {
                $params["error"] = "Upravení recenze se nepodařilo";
            }
            return $params;
        }
    }

     /**
     * Metoda, obsluhující formuláře, související se soubory.
     * 
     * @param type $post
     * @param type $db
     * @param type $login
     * @return array
     */
    function doFile($post, $db, $login) {
        $params = array();
        //smazat soubor   
        if ($post["file"] == "delete") {
            $res = $db->deleteFile($post['idPost']);
            if ($res) {
                $params["message"] = "Soubor byl odstraněn";
            } else {
                $params['error'] = "Smazání souboru se nezdařilo";
            }
            return $params;

            //změnit ikonku        
        } else if ($post["file"] == "icon") {
            $target_dir = "img/";
            $fileName = basename($_FILES["file"]["name"]);
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $ok = 1;

            //povolí jen obrázky
            if ($fileType != "png" && $fileType != "jpeg" && $fileType != "jpg" ) {
                $params["error"] = "Špatný formát obrázku, povoleny: PNG, JPEG, JPG";
                $ok = 0;
            }
            if (getimagesize($_FILES["file"]["tmp_name"]) == false) {
                $params["error"] = "Špatný formát obrázku";
                $ok = 0;
            }

            //velikost
            if ($_FILES["file"]["size"] > 100000) {
                $params["error"] = "Jen soubory do velikosti 100kB jsou povoleny";
                $ok = 0;
            }

            //pokud lze nahrát
            if ($ok == 1) {
                $date = getdate();
                $author = $login->getLogged();
                $fileName = $author["name"] . "_" . $date['yday'] . "-" . $date['year'] . "-" .
                        $date['seconds'] . "-" . $date['minutes'] . "-" . $date['hours'] . $fileType;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $fileName)) {

                    $res = $db->changeIcon($author["name"], $fileName);
                    if ($res) {
                        $params["message"] = "Obrázek byl nahrán";
                    } else {
                        $params["error"] = "Nahrání se nezdařilo";
                    }
                } else {
                    $params["error"] = "Nahrání se nezdařilo";
                }
            }
            return $params;
        }
    }

}
