<?php

class registerController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/register.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}