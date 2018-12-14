<?php

class editPageController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/editPage.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}