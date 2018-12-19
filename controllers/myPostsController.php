<?php

/**
 * Kontroler pro stránku myPosts
 */
class myPostsController extends baseController {
    
    /**
     * Načte stránku members a předá ji do metody render()
     * 
     * @param type $params parametry vytvořené v indexu
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/myPosts.php", $params);
        $this->render($html, $params);
    }
}