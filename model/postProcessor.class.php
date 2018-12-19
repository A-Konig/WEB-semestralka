<?php

/*
 * Třída obsahující metody obsluhující $_POST
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
     * Základní metoda zpracující $_POST, podle hodnot v něm nastavených volá další metody.
     * 
     * @return array obsahující výsledky zpracování: "message" => "zpráva o úspěchu", "error" => "zpráva o neúspěchu"
     */
    function doPost() {

        if (isset($this->post["submit"])) {

            //uživatel    
            if ((isset($this->post["log"]))) {
                return $this->doUser();

            //příspěvek        
            } else if ((isset($this->post["post"]))) {
                return $this->doArticle();

            //recenze        
            } else if ((isset($this->post["rec"]))) {
                return $this->doRec();

            //soubor        
            } else if ((isset($this->post["file"]))) {
                return $this->doFile();

            //nic    
            } else {
                echo 'Nic se nestalo';
            }
        }
    }

    /**
     * Metoda, obsluhující formuláře, související s uživatelem
     * 
     * @return array obsahující výsledky zpracování: "message" => "zpráva o úspěchu", "error" => "zpráva o neúspěchu"
     */
    private function doUser() {
        $params = array();
        //login    
        if ($this->post["log"] == "login") {
            if ($this->db->authorizeUser($this->post["name"], $this->post["password"])) {
                $toLog = $this->db->getUser($this->post["name"]);

                //není user zablokován?
                $blocked = false;
                if ($toLog['block'] == 1) {
                    $blocked = true;
                }

                if ($blocked == false) {
                    $this->login->login($this->post["name"]);
                    $logged = $this->login->getLogged();
                    $params["user"] = $this->db->getUser($this->post["name"]);
                    header("Location: /index.php?page=home");
                } else {
                    $params["error"] = "Váš účet byl zablokován";
                }
            } else {
                $params["error"] = "Přihlášení se nezdařilo";
            }
            return $params;
         
           //odhlášení 
        } else if ($this->post["log"] == "logout") {
            $this->login->logout();
            header("Location: /index.php?page=logoutPage");
            return $params;

            //registrace
        } else if ($this->post["log"] == "register") {
            $res = $this->db->createUser($this->post["name"], $this->post["login"], $this->post["password"], $this->post["email"], 3);
            if ($res) {
                $this->login->login($this->post["login"]);
                $logged = $this->login->getLogged();
                $params["user"] = $this->db->getUser($this->post["login"]);
                header("Location: /index.php?page=home");
            } else {
                $params["error"] = "Registrace se nezdařila";
            }
            return $params;

            //smazání uživatele
        } else if ($this->post["log"] == "delete") {
            $res = $this->db->deleteUser($this->post["loginUser"]);
            if (!$res) {
                $params["error"] = "Smazání uživatele se nezdařilo";
            } else {
                $params["message"] = "Uživatel byl smazán";
            }
            return $params;
            //zablokování
        } else if ($this->post["log"] == "block") {
            $this->db->blockUser($this->post["loginUser"]);
            $params["message"] = "Uživatel " . $this->post["loginUser"] . " zablokován";
            return $params;

            //odblokování
        } else if ($this->post["log"] == "unblock") {
            $this->db->unblockUser($this->post["loginUser"]);
            $params["message"] = "Uživatel " . $this->post["loginUser"] . " odblokován";
            return $params;

            //změna e-mailu    
        } else if ($this->post["log"] == "changeMail") {
            $user = $this->login->getLogged();
            if ($this->db->authorizeUser($user['name'], $this->post["password"])) {
                $res = $this->db->updateEmail($user['name'], $this->post['email']);
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
        } else if ($this->post["log"] == "changePass") {
            $user = $this->login->getLogged();
            if ($this->db->authorizeUser($user['name'], $this->post["pass1"])) {
                $this->db->updatePassword($user['name'], $this->post['pass2']);
                $params["message"] = "Změna hesla provedena";
            } else {
                $params["error"] = "Špatné heslo";
            }
            return $params;

            //změna jména        
        } else if ($this->post["log"] == "changeName") {
            $log = $this->login->getLogged();
            $res = $this->db->changeName($log['name'], $this->post["name"]);
            if ($res) {
                $params["message"] = "Změna jména provedena";
            } else {
                $params["error"] = "Změna jména se nezdařila";
            }
            return $params;

            //změna role        
        } else if ($this->post["log"] == "setRight") {
            $res = $this->db->updateRight($this->post["login"], $this->post["right"]);
            if ($res == true) {
                $params["message"] = "Role uživatele " . $this->post["login"] . " změněna";
            } else {
                $params["error"] = "Role se nepodařila změnit";
            }
            return $params;
        }
    }

     /**
     * Metoda, obsluhující formuláře, související s příspěvky
     * 
     * @return array obsahující výsledky zpracování: "message" => "zpráva o úspěchu", "error" => "zpráva o neúspěchu"
     */
    private function doArticle() {
        $params = array();
        //nový příspěvek   
        if ($this->post["post"] == "newPost") {
            $author = $this->login->getLogged();
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

            $res = $this->db->addPost($author["name"], $this->post["headline"], $this->post["content"], $fileName);
            if (!$res) {
                $params["error"] .= "Přidání příspěvku se nezdařilo";
            } else {
                $params["message"] = "Příspěvek byl přidán";
            }
            return $params;

            //schválit příspěvek
        } else if ($this->post["post"] == "publish") {
            $res = $this->db->publishPost($this->post["idPost"]);

            if ($res == true) {
                $params["message"] = "Příspěvek byl publikován";
            } else {
                $params["error"] = "Publikování příspěvku se nezdařilo";
            }
            return $params;

            //upravit příspěvek        
        } else if ($this->post["post"] == "edit") {
            $fileName = null;
            $ok = 0;
            if (isset($_FILES["file"])) {
                if ($_FILES["file"]["name"] != null) {
                    $author = $this->login->getLogged();
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

            $res = $this->db->editPost($this->post["idPost"], $this->post["headline"], $this->post["content"], $fileName);
            if ($res == true) {
                $params["message"] = "Příspěvek byl upraven";
                
                $recs = $this->db->getRecs($this->post["idPost"]);
                if ($recs != null) {
                    foreach ($recs as $rec) {
                        if ($this->db->getOneRec($rec['id']) != null) {
                            $this->db->notUpToDate($rec['id']);
                        }
                    }
                }
            } else {
                $params["error"] = "Editace příspěvku se nezdařila";
            }
            return $params;

            //smazat příspěvek
        } else if ($this->post["post"] == "delete") {

            $recs = $this->db->getRecs($this->post["idPost"]);
            if ($recs != null) {
                foreach ($recs as $rec) {
                    $this->db->deleteRec($rec['id']);
                }
            }
            
            $res = $this->db->deletePost($this->post["idPost"]);
            if (!$res) {
                $params["error"] = "Smazání příspěvku se nezdařilo";
            } else {
                $params["message"] = "Příspěvek byl smazán";
            
            }
            return $params;

            //zamítnout příspěvek
        } else if ($this->post["post"] == "deny") {
            $res = $this->db->denyPost($this->post["idPost"]);
            
            if (!$res) {
                $params["error"] = "Zamítnutí příspěvku se nezdařilo";
            } else {
                $params["message"] = "Příspěvek byl zamítnut";
            }
            return $params;

            //přiřazení recenzenta
        } else if ($this->post["post"] == "setRec") {
            
            $this->db->updateRec($this->post["idPost"], $this->post["rec1"], $this->post["rec2"], $this->post["rec3"]);
            
            $params["message"] = "Recenzent byl přiřazen";
            return $params;
        }
    }

     /**
     * Metoda, obsluhující formuláře, související s recenzemi.
 
     * @return array obsahující výsledky zpracování: "message" => "zpráva o úspěchu", "error" => "zpráva o neúspěchu"
     */
    private function doRec() {
        $params = array();
        //nová recenze
        if ($this->post["rec"] == "newRec") {
            $author = $this->login->getLogged();
            $published = $this->db->getRecs($this->post["idPost"]);
            
            if ($published != null) {
                foreach ($published as $rec) {
                    if ($rec['autor']==$author['name']) {
                        $params["error"] = "Přidání recenze se nezdařilo";
                        return $params;
                    }
                }
            }
            
            $res = $this->db->addRec($author['name'], $this->post["content"], $this->post["lang"], $this->post["orig"], $this->post["summary"], $this->post["idPost"]);

            if ($res) {
                $params["message"] = "Recenze byla publikována";
            } else {
                $params["error"] = "Došlo k chybě při přidávání recenze";
            }
            return $params;

            //smazat  
        } else if ($this->post["rec"] == "delete") {
            $res = $this->db->deleteRec($this->post["idRec"]);
            if (!$res) {
                $params["error"] = "Smazání recenze se nezdařilo";
            } else {
                $params["message"] = "Recenze byla smazána";
            }
            return $params;

            //edit       
        } else if ($this->post["rec"] == "edit") {
            $res = $this->db->editRec($this->post["idRec"], $this->post["content"], $this->post["summary"], $this->post["lang"], $this->post["orig"]);
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
     * @return array obsahující výsledky zpracování: "message" => "zpráva o úspěchu", "error" => "zpráva o neúspěchu"
     */
    function doFile() {
        $params = array();
        //smazat soubor   
        if ($this->post["file"] == "delete") {
            $res = $this->db->deleteFile($this->post['idPost']);
            if ($res) {
                $params["message"] = "Soubor byl odstraněn";
            } else {
                $params['error'] = "Smazání souboru se nezdařilo";
            }
            return $params;

            //změnit ikonku        
        } else if ($this->post["file"] == "icon") {
            $target_dir = "img/";
            $fileName = basename($_FILES["file"]["name"]);
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $ok = 1;

            //povolí jen obrázky
            if ($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif") {
                $params["error"] = "Špatný formát obrázku, povoleny: PNG, JPEG, GIF";
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
                $author = $this->login->getLogged();
                $fileName = $author["name"] . "_" . $date['yday'] . "-" . $date['year'] . "-" .
                        $date['seconds'] . "-" . $date['minutes'] . "-" . $date['hours'] . $fileType;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $fileName)) {

                    $res = $this->db->changeIcon($author["name"], $fileName);
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
