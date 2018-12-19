<?php

/**
 * Kontroler pro stránku toPublish
 */
class toPublishController extends baseController {
    
    /**
     * Načte stránku toPublish a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/toPublish.php", $params);
        $this->render($html, $params);
    }
}