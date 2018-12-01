<?php
include_once('session.class.php');

/**
 * Třída obstarávající funkce související s uživatelem
 * 
 */
class User {
    //session
    private $session;
    
    /*
     * Konstruktor
     */
    public function __construct() {
        $this->session = new Session;
    }
    
    /*
     * Funkce, která zapíše údaje do session
     * @param $user username přihlášeného uživatele 
     */
    public function login($user) {
        $time = date("H:i:s, d.m.Y");
        $this->session->startSession($user, $time);
    }
    
    /*
     * Funkce, která zjistí jestli je někdo přihlášen nebo ne
     * @return boolean
     */
    public function isLogged() {
        if ((isset($this->session))&&($this->session->exists())) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * Fuknce, která vrátí informace o přihlášeném uživateli
     * @return array
     */
    public function getLogged() {
        if ((isset($this->session))&&($this->session->exists())) {
            return $this->session->readSession();
        } else {
            return null;
        }
    }
    
    /*
     * Funkce, která odhlásí uživatele
     */
    public function logout() {
        if ((isset($this->session))&&($this->session->exists())) {
             $this->session->endSession();
        }
    }
}
