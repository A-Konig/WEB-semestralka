<?php

class settingsController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/settings.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}