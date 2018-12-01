<?php

$allUsers = $params['db']->allUsers();

echo '<div class="page">';
//pro přihlášené uživatele
if ($user != null) {
    
    if ($allUsers != null) {
        foreach ($allUsers as $index) {
            echo '<div class="listingBox">';
                echo '<div class="itemName">'.$index['login'].'</div>';
                echo '<div class="item row">';
                    echo '<div class ="img"><img src="../img/empty.png" width="70px" height="90úx" ></div>';
                    echo $index['jmeno'].'<br>'.$index['roleData']['nazev'];
                echo '</div>';
            echo '</div>';
        }
    }
    
//pro nepřihlášené uživatele    
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a class="button" href="index.php?page=login">Login</a>';
}

echo '</div>';
echo '<br>';

