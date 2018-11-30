<?php
//vlastní funkce
include_once("inc/functions.php");

//nacte param z url
if (isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"];
} else{
    $page = "home";
}
 
//struktura stránek
$pages = array();
$pages["home"] = "Úvod";
//$pages["contacts"] = "Kontakt";
$pages["members"] = "Členové";
$pages["submissions"] = "Příspěvky";
$pages["faq"] = "FAQ";

$pg = array();
$pg["login"] = "Login";
$pg["register"] = "Registrovat";
$pg["newPost"] = "Nový příspěvek";
$pg["terms"] = "Podmínky";

if(array_key_exists($page, $pages)) {
    $filename = $page.".php";
} else if(array_key_exists($page, $pg)) {
    $filename = $page.".php";
} else {
    $filename = "error.php";
}

$params = array();
$params["a"] = 5;
$params["b"] = 3;
$contents = phpWrapperFromFile("pages/".$filename , $params);
$user = phpWrapperFromFile("pages/unlogged.php" );

require_once __DIR__.'/vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('sablony');

$twig = new Twig_Environment($loader);

echo $twig->render("basic.html", array("user" => $user, "contents" => $contents, "pages" => $pages));

//echo ''.$obsah;