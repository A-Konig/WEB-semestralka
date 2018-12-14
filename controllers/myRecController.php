<?php

class myRecController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/myRec.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}