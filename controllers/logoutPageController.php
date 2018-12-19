<?php

/**
 * Kontroler pro logout stránku
 */
class logoutPageController extends baseController {
    
    /**
     * Načte logout stránku a předá ji do metody render()
     * 
     * @param type $params
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/logoutPage.php", $params);
        $this->render($html, $params);
    }
    
}
