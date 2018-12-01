<?php

echo '<div class="page">';

//pro přihlášené uživatele
if ($user != null) {
    echo 'Jste přihlášen jako uživatel ' . $params["user"]["login"]
    . '<br><br>';
    echo 'Nejste to vy?'
    . '<br><br>';
    echo '<form action="" method="POST">';
    echo '<input type="hidden" name="log" value="logout">';
    echo '<input class="inputButton" type="submit" name="submit" value="Odhlásit">';
    echo '</form>';

//pro nepřihlášené uživatele
} else {

    echo '<h2>Přihlášení:</h2>';
    echo '<form class = "submit" action="" method="POST">';

    echo '<span class="formHead">Login:</span> <input type="text" name="name"><br>';
    echo '<span class="formHead">Heslo:</span> <input type="password" name="password"><br>';
    
    echo '<input type="hidden" name="log" value="login"><br>';
    echo '<input class="inputButton" type="submit" name="submit" value="Přihlásit">';
    echo '</form>';
    echo '<br>';
}

echo '</div>';