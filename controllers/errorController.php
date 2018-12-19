<?php

/**
 * Kontroler pro stránku error
 */
class errorController extends baseController {
    
    /**
     * Načte error stránku a předá ji do metody render()
     * 
     * @param type $params
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/error.php", $params);
        $this->render($html, $params);
    }
}