<?php

class faqController extends baseController {
    
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/faq.php", $params);
        //$menu = $params["menu"];
        $this->render($html, $params);
    }
}