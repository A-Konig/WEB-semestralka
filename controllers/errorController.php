<?php

class errorController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/error.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}