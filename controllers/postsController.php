<?php

class postsController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/posts.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}