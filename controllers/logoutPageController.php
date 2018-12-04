<?php

class logoutPageController extends baseController {
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/logoutPage.php", $params);
        $this->render($html, $params);
    }
    
}
