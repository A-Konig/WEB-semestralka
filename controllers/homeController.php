<?php

class homeController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/home.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}