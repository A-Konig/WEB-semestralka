<?php

/**
 * Kontroler pro stránku editPage
 */
class editPageController extends baseController {
    
    /**
     * Načte editPage a předá ji do metody render()
     * 
     * @param type $params
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/editPage.php", $params);

        $this->render($html, $params);
    }
}