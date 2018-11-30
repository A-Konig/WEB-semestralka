<?php

class loginController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/login.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}