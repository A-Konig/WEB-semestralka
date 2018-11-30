<?php

class membersController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/members.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}