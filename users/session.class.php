<?php

/*
 * Třída, obstarávající funkce související se session
 */
class Session {
    
    /*
     * Konstruktor
     */
    public function __construct() {
        session_start();
    }
    
    /*
     * Funkce, která započne session a uloží do jí $user na pozici user a $data na pozici data
     * @param $user jméno uživatele
     * @param $data předáváná data  
     */
    public function startSession($user, $data) {
        $_SESSION["user"] = $user;
        $_SESSION["data"] = $data;
    }
    
    /*
     * Ukončí session
     */
    public function endSession() {
        unset($_SESSION["user"]);
        unset($_SESSION["data"]);
    }
    
    /*
     * Přečte data uložená v session
     * @return $data data ze session
     */
    public function readSession() {
        if ($this->exists()) {
            $data = array();
            $data["name"] = $_SESSION["user"];
            $data["data"] = $_SESSION["data"];
            return $data;
        } else {
            return null;
        }
    }
    
    /*
     * Zjistí, zda session existuje nebo ne
     * @return boolean 
     */
    public function exists() {
        return isset($_SESSION["user"]);
    }
    
}
