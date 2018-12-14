<?php

class myPostsController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/myPosts.php", $params);
        $this->render($html, $params);
    }
}