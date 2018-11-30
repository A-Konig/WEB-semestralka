<?php

class newPostController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/newPost.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}