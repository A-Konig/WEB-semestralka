<?php

class toPublishController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/toPublish.php", $params);
        $this->render($html, $params);
    }
}