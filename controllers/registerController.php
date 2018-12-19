<?php

/**
 * Kontroler pro stránku reguster
 */
class registerController extends baseController {
    
    /**
     * Načte stránku register a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/register.php", $params);
        $this->render($html, $params);
    }
}