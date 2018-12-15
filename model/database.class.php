<?php

/*
 * Třída obstarávající interakci s databází 
 */
class Database extends baseModel {
        
    /**
     * Ověří správnost přihlašovacího jména a hesla
     * 
     * @param $login přihlašovací jméno
     * @param $passwprd heslo
     * @return boolean
     */
    public function authorizeUser($login, $password) {
        $user = $this->getUser($login);

        if (($user != null) && (password_verify($password, $user['heslo'])) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Metoda, která vybere všechny uživatele a jejich uživatelská práva z databáze
     * @return pole s uživateli
     */
    public function allUsers() {
        $table_name = "uzivatele";
        $where_array = array();
        
        $res = $this->DBSelectAll($table_name, "*", $where_array);
        
        $i = 0;
        $users = array();        
        if ($res != null) {
            foreach ($res as $index) {
                $index = $this->appendRight($index);
                $users[$i] = $index;
                $i++;
            }
        }

        return $users;
    }
    
    /**
     * Metoda, která z databáze zjistí dostupné údaje o daném uživateli.
     * @params $login login uživatele
     * @return pole s daty o uživateli nebo null
     */
    public function getUser($login) {
        $table_name = "uzivatele";
        $where_array = array();
        $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
        $users = $this->DBSelectOne($table_name, "*", $where_array);
        $user = $this->appendRight($users);
        return $user;
    }

    /**
     * Vytvoří nového uživatele v databázi
     * 
     * @param type $jmeno   jméno
     * @param type $login   login  
     * @param type $heslo   heslo
     * @param type $email   email
     * @param type $role    role
     * @return boolean jestli operace proběhla nebo ne
     */
    public function createUser($jmeno, $login, $heslo, $email, $role) {
        $jmenoE = str_replace("'","''",$jmeno);
        $loginE = str_replace("'","''",$login);
        $emailE = str_replace("'","''",$email);        
        
        if ($this->getUser($login) == null) {
            $table_name = "uzivatele";
            $passH = password_hash($heslo, PASSWORD_DEFAULT);        
            $item = array("login" => "'$loginE'", "jmeno" => "'$jmenoE'", "heslo" => "'$passH'", "email" => "'$emailE'", "role" => "$role");
        
            $res = $this->DBInsert($table_name, $item);
        
            if ($res != null) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Funkce co změní v databázi u daného uživatele heslo
     * 
     * @param type $login   login uživatele
     * @param type $email   nové heslo
     * @return boolean  jestli došlo k operaci nebo ne
     */
    public function updateEmail($login, $email) {
        if ($this->getUser($login) != null) {
            $email = str_replace("'","''",$email);
        
            $table_name = "uzivatele";
            $toUpdate = array("email" => "'$email'");
            $where_array = array();
            $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
            $this->DBUpdate($table_name, $toUpdate, $where_array);
            
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Funkce co změní v databázi u daného uživatele heslo.
     * 
     * @param type $login   login uživatele
     * @param type $password    nové heslo
     */
    public function updatePassword($login, $password) {
        if ($this->getUser($login) != null) {
            $table_name = "uzivatele";
            $passH = password_hash($password, PASSWORD_DEFAULT);
            $toUpdate = array("heslo" => "'$passH'");
            $where_array = array();
            $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
            $this->DBUpdate($table_name, $toUpdate, $where_array);
        
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Funkce, která zablokuje uživatele
     * 
     * @params type $login login uživatele
     * @return boolean informace jestli operace proběhla nebo ne
     */
    public function blockUser($login) {
        if ($this->getUser($login) != null) {
            $table_name = "uzivatele";
            $toUpdate = array("block" => "1");
            $where_array = array();
            $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
            $this->DBUpdate($table_name, $toUpdate, $where_array);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Funkce, která odblokuje uživatele
     * 
     * @param type $login
     * @return boolean informace jestli operace proběhla nebo ne
     */
    public function unblockUser($login) {
        if ($this->getUser($login) != null ){
            $table_name = "uzivatele";
            $toUpdate = array("block" => "0");
            $where_array = array();
            $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
            $this->DBUpdate($table_name, $toUpdate, $where_array);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Metoda, která smaže uživatele z databáze
     * 
     * @param type $login   login uživatele
     * @return boolean  jestli akce proběhla
     */
    public function deleteUser($login) {
        if ($this->getUser($login) != null) {
            $table_name = "uzivatele";
            $where_array = array();
            $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
            $this->DBDelete($table_name, $where_array, null);

            if ($this->getUser($login) == null) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Metoda co do pole reprezentující uživatele přidá na index pravo pole obsahující informace o jeho přístupovém právu
     * 
     * @param array $user   pole obsahující informace o uživateli
     * @return type pole obsahující informace o uživateli, na indexu roleData se nacházejí informace o jeho přístupovém právu
     */
    private function appendRight($user) {
        if ($user != null) {
            $pravo = $this->getRight($user['role']);
            $user['roleData'] = $pravo;
        }
        return $user;
    }

    /**
     * Metoda, která vybere veškerá přístupová práva z databáze
     * @return type
     */
    public function allRights() {
        $table_name = "role";
        $where_array = array();
        
        $rights = $this->DBSelectAll($table_name, "*", $where_array);
        return $rights;
    }

    /** 
     * Metoda která vrací infomace o daném přístupovém právu
     * 
     * @params id práva
     * @returns array s daty o právu
     */
    public function getRight($id) {
        $table_name = "role";
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $right= $this->DBSelectOne($table_name, "*", $where_array);
        if ($right != null) {
            return $right;
        } else {
            return null;
        }
    }
    
    /**
     * Metoda, která změní přístupové právo zadanému uživateli.
     * 
     * @param type $login   login uživatele
     * @param type $rightId nová hodnota přístupového práva
     */
    public function updateRight($login, $rightId) {
        $table_name = "uzivatele";
        $toUpdate = array("role" => "$rightId");
        $where_array = array();
        $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }

    /**
     * Metoda, která získá informace o všech příspěvcích v databázi
     * 
     * @return array    pole s informacemi o příspěvcích
     */
    public function allPosts() {
        $table_name = "prispevky";
        $where_array = array();
        
        $res = $this->DBSelectAll($table_name, "*", $where_array);
        return $res;
    }

    /**
     * Metoda, která získá konkrétní příspěvek z databáze
     * 
     * @param type $id  id příspěvku
     * @return array    pole s informacemi o daném příspěvku
     */
    public function getPost($id) {
        $table_name = "prispevky";
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $res = $this->DBSelectOne($table_name, "*", $where_array);
        return $res;
    }

    /**
     * Metoda, která přidá nový příspěvek do databáze
     * 
     * @param type $login   login uživatele
     * @param type $title   nadpis článku
     * @param type $content obsah článku
     * @return boolean  zda operace proběhla nebo ne
     */
    public function addPost($login, $title, $content) {
        $user = $this->getUser($login);
        $table_name = "prispevky";
        
        $loginE = str_replace("'","''",$login);
        $titleE = str_replace("'","''",$title); 
        $contentE = str_replace("'","''",$content); 
        
        if ($user != null) {
            $item = array("nazev" => "'$titleE'", "obsah" => "'$contentE'", "autor" => "'$loginE'", "datum" => "CURRENT_DATE()");
        
            $res = $this->DBInsert($table_name, $item);
            
            if ($res != null ) {
                return true;
            } else {
                return false;
            }
            
        } else {
            return false;
        }
    }
    
    /**
     * Metoda, která smaže příspěvek z databáze.
     * 
     * @param type $id  id příspěvku
     * @return boolean  zda operace proběhla nebo ne
     */
    public function deletePost($id) {
        if ($this->getPost($id) != null) {
            $table_name = "prispevky";
            $where_array = array();
            $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
            
            $this->DBDelete($table_name, $where_array, null);
            
            if ($this->getPost($id) == null) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Metoda, která publikuje příspěvek.
     * 
     * @param type $id  id příspěvku
     */
    public function publishPost($id){
        $table_name = "prispevky";
        $toUpdate = array("schvaleny" => "1");
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    /**
     * Metoda, která upraví daný příspěvek v databázi.
     * 
     * @param type $id  id příspěvku
     * @param type $headline    nadpis příspěvku
     * @param type $content     text příspěvku
     * @return boolean  informace zda operace proběhla nebo ne 
     */
    public function editPost($id, $headline, $content) {
        $post = $this->getPost($id);
        
        if ($post != null) {
            $headline = str_replace("'","''",$headline);
            $content = str_replace("'","''",$content);
        
            $table_name = "prispevky";
            $toUpdate = array("nazev" => "'$headline'", "obsah" => "'$content'");
            $where_array = array();
            $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
            $this->DBUpdate($table_name, $toUpdate, $where_array);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Metoda, která vybere všechny recenze z databáze
     * 
     * @return array    pole obsahující informace o recenzích
     */
    public function allRecs() {
        $table_name = "hodnoceni";
        $where_array = array();
        
        $res = $this->DBSelectAll($table_name, "*", $where_array);
        
        return $res;
    }
    
    /**
     * Metoda, která nastaví recenzenta číslo $num na novou hodnotu
     * 
     * @param type $recenzent   nový recenzent
     * @param type $postId  id příspěvku
     * @param type $num číslo recenzenta
     */
    public function updateRec($recenzent, $postId, $num) {
        $table_name = "prispevky";
        if ($recenzent != null) {
            $recenzent = str_replace("'","''",$recenzent); 
            
            $toUpdate = array("rec$num" => "'$recenzent'");
        } else {
            $toUpdate = array("rec$num" => "NULL");
        }
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $postId);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    /**
     * Metoda, která přidá novou recenzi do databáze
     * 
     * @param type $login   login autora
     * @param type $content obsah recenze
     * @param type $lang    hodnocení jazyka (celá čísla z intervalu <1-5>)
     * @param type $orig    hodnocení originality (celá čísla z intervalu <1-5>)
     * @param type $overview    celkové hodnocení (celá čísla z intervalu <1-5>)
     * @param type $idPost  id příspěvku
     * @return boolean  informace, zda k operaci došlo nebo ne
     */
    public function addRec($login, $content, $lang, $orig, $overview, $idPost) {
        $user = $this->getUser($login);
        $table_name = "hodnoceni";
        
        if ($user != null) {
            $login = str_replace("'","''",$login);
            $content = str_replace("'","''",$content); 
            
            $item = array("autor" => "'$login'", "obsah" => "'$content'", "celkove" => "'$overview'", "jazyk" => "'$lang'", "originalita" => "'$orig'", "prispevek" => "'$idPost'",  "datum" => "CURRENT_DATE()");
        
            $this->DBInsert($table_name, $item);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 
     * @param type $id
     * @param type $content
     * @param type $summary
     * @param type $lang
     * @param type $orig
     */
    public function editRec($id, $content, $summary, $lang, $orig) {
        $table_name = "hodnoceni";
        $toUpdate = array("obsah" => "'$content'", "celkove" => "$summary", "jazyk" => "$lang", "originalita" => "$orig");
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    /**
     * Metoda, která načte všechny recenzenze k zadanému příspěvku v databázi
     * 
     * @param type $idPost  id příspěvku
     * @return array    pole s recenzemi
     */
    public function getRecs($idPost) {
        if ($this->getPost($idPost) != null) {
            $table_name = "hodnoceni";
            $where_array = array();
            $where_array[] = array("column" => "prispevek", "symbol" => "=", "value" => $idPost);
        
            $res = $this->DBSelectAll($table_name, "*", $where_array);
            return $res;
        }
    }
    
    /**
     * Metoda, která vrací informace o jedné konkrétní recenzi
     * 
     * @param type $id  id recenze
     * @return array    pole s informacemi o recenzi
     */
    public function getOneRec($id) {
        $table_name = "hodnoceni";
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $res = $this->DBSelectOne($table_name, "*", $where_array);
        return $res;
    }
    
    /**
     * Metoda, která smaže danou recenzi
     * 
     * @param type $id  id recenze
     * @return boolean informace zda k operaci došlo či ne
     */
    public function deleteRec($id) {
        if ($this->getOneRec($id) != null) {
            $table_name = "hodnoceni";
            $where_array = array();
            $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
            $this->DBDelete($table_name, $where_array, null);
            return true;
        }
        return false;
    }

}