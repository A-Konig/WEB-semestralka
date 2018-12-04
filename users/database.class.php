<?php

/*
 * Třída obstarávající interakci s databází 
 */

class Database {
    /*
     * PDO
     */

    private $db;

    /*
     * Konstruktor
     */

    public function __construct() {
        $db_server = DB_SERVER;
        $db_name = DB_NAME;
        $db_user = DB_USER;
        $db_pass = DB_PASS;

        try {
            $this->db = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_pass);
        } catch (exception $e) {
             echo '<div class="alert alert-warning">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Bez připojení k databázi!</strong> Některé funkce webu nemusí být dostupné 
                </div>';
        }
    }

    /*
     * Metoda apro vykonání SQL příkazů
     * @param $q obsahuje string s SQL příkazem
     */

    private function execute($q) {
        if (isset($this->db)) {
            $res = $this->db->query($q);
            if (!$res) {
                $error = $this->db->errorInfo();
                echo $error[2];
//smazat            
                echo 'chyba při výkonu: ' . $q;
                return null;
            } else {
                $res = $res->fetchAll();
                return $res;
            }
        } else {
            return null;
        }
    }

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
        $q = "SELECT * FROM akonig_uzivatele";
        $res = $this->execute($q);
        
        if ($res != null) {
            $users = array();
            //ke zaždému uživateli zjistí jeho přístupové právo
            $i = 0;
            foreach ($res as $index) {
                $index = $this->appendRight($index);
                $users[$i] = $index;
                $i++;
            }
            return $users;
        } else {
            return null;
        }
    }

    /*
     * Metoda, která z databáze zjistí dostupné údaje o daném uživateli.
     * @params $login login uživatele
     * @return
     */
    public function getUser($login) {
        $q = "SELECT * FROM akonig_uzivatele WHERE login = '" . $login . "';";
        $users = $this->execute($q);

        if ($users != null) {
            $user = $this->appendRight($users['0']);
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
        if (!(isset($this->db))) {
            return false;
        }
        if ($this->getUser($login) == null) {
            $q = "INSERT INTO akonig_uzivatele(login,jmeno,heslo,email,role)
                VALUES ('$login','$jmeno','$heslo','$email',$role)";
            $this->execute($q);
        } else {
            return false;
        }
    }

    /*
     * Metoda, která smaže uživatele z databáze
     */

    public function deleteUser($login) {
        if (!(isset($this->db))) {
            return false;
        }
        if ($this->getUser($login) != null) {
            $q = "DELETE FROM akonig_uzivatele WHERE login='$login';";
            $this->execute($q);
            return true;
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
        $q = "SELECT * FROM akonig_role;";
        $res = $this->execute($q);
        return $res;
    }

    /*
     * Metoda která vrací infomace o daném přístupovém právu
     * @params id práva
     * @returns array s daty o právu
     */
    public function getRight($id) {
        $q = "SELECT * FROM akonig_role WHERE id = '" . $id . "';";
        $pravo = $this->execute($q);
        if ($pravo != null) {
            return $pravo['0'];
        } else {
            return null;
        }
    }

//TODO    
    public function allPosts() {
        $q = "SELECT * FROM akonig_prispevky;";
        $res = $this->execute($q);
        return $res;
    }

    public function getPost($id) {
        $q = "SELECT * FROM akonig_prispevky WHERE id = '". $id ."';";
        $post = $this->execute($q);
        if ($post != null) {
            return $post['0'];
        } else {
            return null;
        }
    }

    public function addPost($login, $title, $content, $tags) {
        if (!(isset($this->db))) {
            return false;
        }
        $user = $this->getUser($login);

        if ($user != null) {
            $id = $user['ID'];        
            $q = "INSERT INTO akonig_prispevky(autor,nazev,obsah,tag)
                VALUES ($id,'$title','$content','$tags')";
            $this->execute($q);
            return true;
        } else {
            return false;
        }
    }

    public function appendAutor() {
        
        
    }

}
