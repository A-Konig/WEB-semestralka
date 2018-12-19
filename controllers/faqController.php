<?php

/**
 * Kontroler pro stránku faq
 */
class faqController extends baseController {
    
    /**
     * Načte stránku s faq a předá ji do metody render()
     * 
     * @param type $params
     */
    public function indexAction($params) {
        $html =  phpWrapperFromFile("pages/faq.php", $params);
        $this->render($html, $params);
    }
}