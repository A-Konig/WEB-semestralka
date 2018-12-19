<?php

/**
 * Kontroler pro login stránku
 */
class loginController extends baseController {
    
    /**
     * Načte login stránku a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/login.php", $params);
        $this->render($html, $params);
    }
}