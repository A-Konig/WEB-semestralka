<?php

$allUsers = $params['db']->allUsers();

echo '<div class="container-fluid">';
//pro přihlášené uživatele
if ($user != null) {
    
    if ($allUsers != null) {
        foreach ($allUsers as $index) {
            echo '<div class="container-fluid">';
                echo '<div class="well well-sm well-top">'.$index['login'].'</div>';
                echo '<div class="well well-bottom well-member">';
                    echo '<div class ="img"><img src="../img/empty.png" width="70px" height="90px" ></div>';
                    echo $index['jmeno'].'<br>'.$index['roleData']['nazev'];
                echo '</div>';
            echo '</div>';
        }
    }
    
//pro nepřihlášené uživatele    
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';
echo '<br>';