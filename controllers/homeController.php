<?php

/**
 * Kontroler pro home stránku
 */
class homeController extends baseController {
    
    /**
     * Načte home stránku a předá ji do metody render()
     * 
     * @param type $params
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/home.php", $params);
        $this->render($html, $params);
    }
}