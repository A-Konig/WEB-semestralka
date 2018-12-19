<?php

/**
 * Kontroler pro stránku viewPost
 */
class viewPostController extends baseController {
    
    /**
     * Načte stránku viewPost a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/viewPost.php", $params);
        $this->render($html, $params);
    }
}