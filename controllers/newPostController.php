<?php

/**
 * Kontroler pro stránku newPost
 */
class newPostController extends baseController {
    
    /**
     * Načte newPost stránku a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/newPost.php", $params);
        $this->render($html, $params);
    }
}