<?php

/**
 * Kontroler pro stránku members
 */
class membersController extends baseController {
    
    /**
     * Načte stránku members a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/members.php", $params);
        $this->render($html, $params);
    }
}