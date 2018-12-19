<?php

/**
 * Kontroler pro stránku posts
 */
class postsController extends baseController {
    
    /**
     * Načte stránku posts a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/posts.php", $params);
        $this->render($html, $params);
    }
}