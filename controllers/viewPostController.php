<?php

class viewPostController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/viewPost.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}