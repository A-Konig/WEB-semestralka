<?php

class baseController {
    private $twig;
    
    public function __construct($twig) {
        $this->twig = $twig;
    }
    
    public function render($contents, $params) {
        $menu = $params["menu"];
        $user =  phpWrapperFromFile("pages/unlogged.php", $params);
        echo $this->twig->render("basic.php", array("user" => $user, "contents" => $contents, "pages" => $menu));
    }
    
}
