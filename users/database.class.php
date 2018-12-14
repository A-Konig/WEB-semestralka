<?php

/*
 * Třída obstarávající interakci s databází 
 */
class Database extends baseModel {
    //private $db;    
    
    /*
     * Ověří správnost přihlašovacího jména a hesla
     * @param $login přihlašovací jméno
     * @param $heslo heslo
     * @return boolean
     */
    public function authorizeUser($login, $heslo) {
        $user = $this->getUser($login);
        if (($user != null) && ($user['heslo'] == $heslo)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Metoda, která vybere všechny uživatele a jejich uživatelská práva z databáze
     * @return pole s uživateli
     */
    public function allUsers() {
        $table_name = "uzivatele";
        $where_array = array();
        
        $i = 0;
        $users = array();
        $res = $this->DBSelectAll($table_name, "*", $where_array);
        foreach ($res as $index) {
            $index = $this->appendRight($index);
            $users[$i] = $index;
            $i++;
        }

        return $users;
    }
    
    /*
     * Metoda, která z databáze zjistí dostupné údaje o daném uživateli.
     * @params $login login uživatele
     * @return
     */
    public function getUser($login) {
        $table_name = "uzivatele";
        $where_array = array();
        $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
        $users = $this->DBSelectOne($table_name, "*", $where_array);
        if ($users != null) {
            $user = $this->appendRight($users);
            return $user;
        } else {
            return null;
        }
    }

    /*
     * Vytvoří nového uživatele v databázi
     * @return boolean jestli operace proběhla nebo ne
     */
    public function createUser($jmeno, $login, $heslo, $email, $role) {
        if ($this->getUser($login) == null) {
            $table_name = "uzivatele";
            $item = array("login" => "'$login'", "jmeno" => "'$jmeno'", "heslo" => "'$heslo'", "email" => "'$email'", "role" => "$role");
        
            $res = $this->DBInsert($table_name, $item);
        
            //printr($uzivatele);
            if ($res != null) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function updateEmail($login, $email) {
        $table_name = "uzivatele";
        $toUpdate = array("email" => "'$email'");
        $where_array = array();
        $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    public function updatePassword($login, $password) {
        $table_name = "uzivatele";
        $toUpdate = array("heslo" => "'$password'");
        $where_array = array();
        $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    /*
     * Metoda, která smaže uživatele z databáze
     */

    public function deleteUser($login) {
        if ($this->getUser($login) != null) {
            $table_name = "uzivatele";
            $where_array = array();
            $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
            $this->DBDelete($table_name, $where_array, null);

            return true;
        } else {
            return false;
        }
    }

    /*
     * Metoda co do pole reprezentující uživatele přidá na index pravo pole obsahující informace o jeho přístupovém právu
     */

    private function appendRight($user) {
       $pravo = $this->getRight($user['role']);
       $user['roleData'] = $pravo;
       return $user;
    }

    /*
     * Metoda, která vybere veškerá přístupová práva z databáze
     * @returns pole s veškerými daty
     */
    public function allRights() {
        $table_name = "role";
        $where_array = array();
        
        $rights = $this->DBSelectAll($table_name, "*", $where_array);
        return $rights;
    }

    /*
     * Metoda která vrací infomace o daném přístupovém právu
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
    
    public function updateRight($login, $rightId) {
        $table_name = "uzivatele";
        $toUpdate = array("role" => "$rightId");
        $where_array = array();
        $where_array[] = array("column" => "login", "symbol" => "=", "value" => $login);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }

//TODO    
    public function allPosts() {
        $table_name = "prispevky";
        $where_array = array();
        
        $res = $this->DBSelectAll($table_name, "*", $where_array);
        
        return $res;
    }

    public function getPost($id) {
        $table_name = "prispevky";
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $res = $this->DBSelectOne($table_name, "*", $where_array);
        if ($res != null) {
            return $res;
        } else {
            return null;
        }
    }

    public function addPost($login, $title, $content, $tags) {
        $user = $this->getUser($login);
        $table_name = "prispevky";
        
        if ($user != null) {
            $item = array("nazev" => "'$title'", "obsah" => "'$content'", "tag" => "'$tags'", "autor" => "'$login'");
        
            $this->DBInsert($table_name, $item);
            //printr($uzivatele);
            return true;
        } else {
            return false;
        }
    }
    
    public function deletePost($id) {
        if ($this->getPost($id) != null) {
            $table_name = "prispevky";
            $where_array = array();
            $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
            $this->DBDelete($table_name, $where_array, null);
            return true;
        } else {
            return false;
        }
    }
    
    public function publishPost($id){
        $table_name = "prispevky";
        $toUpdate = array("schvaleny" => "1");
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    public function editPost($id, $headline, $content, $tags) {
        $table_name = "prispevky";
        $toUpdate = array("nazev" => "'$headline'", "obsah" => "'$content'", "tag" => "'$tags'");
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    public function allRecs() {
        $table_name = "hodnoceni";
        $where_array = array();
        
        $res = $this->DBSelectAll($table_name, "*", $where_array);
        
        return $res;
    }
    
    public function updateRec($recenzent, $postId, $num) {
        $table_name = "prispevky";
        if ($recenzent != null) {
            $toUpdate = array("rec$num" => "'$recenzent'");
        } else {
            $toUpdate = array("rec$num" => "NULL");
        }
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $postId);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    public function addRec($login, $content, $lang, $orig, $overview, $idPost) {
        $user = $this->getUser($login);
        $table_name = "hodnoceni";
        
        if ($user != null) {
            $item = array("autor" => "'$login'", "obsah" => "'$content'", "celkove" => "'$overview'", "jazyk" => "'$lang'", "originalita" => "'$orig'", "prispevek" => "'$idPost'");
        
            $this->DBInsert($table_name, $item);
            return true;
        } else {
            return false;
        }
    }
    
     public function editRec($id, $content, $summary, $lang, $orig) {
        $table_name = "hodnoceni";
        $toUpdate = array("obsah" => "'$content'", "celkove" => "$summary", "jazyk" => "$lang", "originalita" => "$orig");
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    public function getRecs($idPost) {
        $table_name = "hodnoceni";
        $where_array = array();
        $where_array[] = array("column" => "prispevek", "symbol" => "=", "value" => $idPost);
        
        $res = $this->DBSelectAll($table_name, "*", $where_array);
        if ($res != null) {
            return $res;
        } else {
            return null;
        }
    }
    
    public function getOneRec($id) {
        $table_name = "hodnoceni";
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $res = $this->DBSelectOne($table_name, "*", $where_array);
        if ($res != null) {
            return $res;
        } else {
            return null;
        }
    }
    
     public function publishRec($id){
        $table_name = "hodnoceni";
        $toUpdate = array("schvaleny" => "1");
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        
        $this->DBUpdate($table_name, $toUpdate, $where_array);
    }
    
    public function deleteRec($id) {
        $table_name = "hodnoceni";
        $where_array = array();
        $where_array[] = array("column" => "id", "symbol" => "=", "value" => $id);
        $this->DBDelete($table_name, $where_array, null);
        return true;
    }

}