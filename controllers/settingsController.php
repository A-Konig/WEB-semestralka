<?php

/**
 * Kontroler pro stránku settings
 */
class settingsController extends baseController {
    
    /**
     * Načte stránku settings a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/settings.php", $params);
        $this->render($html, $params);
    }
}