<?php

class termsController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/terms.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}