<?php

/**
 * Základní kontroler
 */
class baseController {
    private $twig;
    
    /**
     * Konstruktor
     * 
     * @param type $twig
     */
    public function __construct($twig) {
        $this->twig = $twig;
    }
    
    /**
     * Načte header s informacemi o uživateli a zavolá metodu render() twigu, které předá parametr $content
     * 
     * @param type $contents
     * @param type $params
     */
    public function render($contents, $params) {
        $menu = $params["menu"];
        $user =  phpWrapperFromFile("pages/unlogged.php", $params);
        echo $this->twig->render("basic.html", array("user" => $user, "contents" => $contents, "pages" => $menu));
    }
    
}
