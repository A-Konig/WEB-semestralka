<?php

/**
 * Kontroler pro stránku terms
 */
class termsController extends baseController {
    
    /**
     * Načte stránku terms a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/terms.php", $params);
        $this->render($html, $params);
    }
}