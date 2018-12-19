<?php

/**
 * Login stránka.
 * 
 */

echo '<div class="container-fluid">';

//výpisy výsledku odeslání formuláře
if (isset($params["error"])) {
    echo '<div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Chyba!</strong> '.$params["error"].'
          </div>';
    unset($params["error"]);
}
if (isset($params["message"])) {
    echo '<div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Úspěch!</strong> ' . $params["message"] . '
          </div>';
    unset($params["message"]);
}

//pro přihlášené uživatele
if ($user != null) {
    echo 'Jste přihlášen jako uživatel ' . $user["login"] . '<br>';
    echo 'Nejste to vy?';
    echo '<form action="" method="POST">';
    echo '<input type="hidden" name="log" value="logout">';
    echo '<input class="btn" type="submit" name="submit" value="Odhlásit">';
    echo '</form>';

//pro nepřihlášené uživatele
} else {

    echo '<h2>Přihlášení:</h2>';
    
    echo '<form class="form-horizontal" action="" method="POST">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Login:</label>
                    <div class="col-sm-3">
                       <input type="text" class="form-control" name="name" placeholder="Uživatelské jméno">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="password">Heslo:</label>
                    <div class="col-sm-3"> 
                        <input type="password" class="form-control" name="password" placeholder="Heslo">
                    </div>
                </div>
                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-7">
                        <input type="hidden" name="log" value="login">
                        <input class="btn" type="submit" name="submit" value="Přihlásit">
                    </div>
                </div>
            </form>';
    
}

echo '</div>';