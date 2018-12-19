<?php

/**
 * Kontroler pro stránku myRec
 */
class myRecController extends baseController {
    
    /**
     * Načte stránku myRec a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/myRec.php", $params);
        $this->render($html, $params);
    }
}