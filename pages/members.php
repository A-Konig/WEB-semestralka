<?php

echo '<div class="page">';

//pro přihlášené uživatele
if ($user != null) {

    echo '<div class="itemName">Login</div>';
    echo '<div class="item row">';
    echo '<div class ="img"><img src="../img/empty.png" width="70px" height="90úx" ></div>';
    echo 'Jméno<br>Role';
    echo '</div>';
    
//pro nepřihlášené uživatele    
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a class="button" href="index.php?page=login">Login</a>';
}

echo '</div>';
echo '<br>';

