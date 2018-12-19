<?php

/**
 * Stránka s možnostmi nastavení uživatelského účtu.
 * Uživatel může změnit svou ikonku, jméno, heslo a e-mail.
 */
echo '<div class="container-fluid">';

//výpisy výsledku odeslání formuláře
if (isset($params["error"])) {
    echo '<div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Chyba!</strong> ' . $params["error"] . '
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
    echo '<h3>Nastavení</h3>';


    echo '
    <div class="row">
    <div class="col-sm-3">';

    //zobrazení ikonky
    $file = 'img/' . $user['ikonka'];

    if (file_exists($file)) {
        echo '<img src="img/' . $user['ikonka'] . '" class="imgEd">';
    } else {
        echo '<img src="img/empty.png" class="imgEd">';
    }

    //zobrazení informací
    echo
    '</div>
    <div class="col-sm-3">
    <table class="table">
        <tbody>
            <tr>
                <td>Login</td>
                <td>' . $user['login'] . '</td>
            </tr>
            <tr>
                <td>Jméno</td>
                <td>' . $user['jmeno'] . '</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>' . $user['email'] . '</td>
            </tr>
            <tr> <td></td> <td></td> </tr>
        </tbody>
    </table>
    </div>
    </div>
        ';

    //změna ikonky
    echo '<h4>Změna ikonky</h4>';
    echo '
    <div class="row">
    <form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label class="control-label col-sm-2" for="pas1">Soubor:</label>
            <div class="col-sm-3">
                <input type="file" class="form-control-static" name="file" id="file">
            </div>
        </div>   

        <div class="form-group">
        <div class="col-sm-offset-2 col-sm-9">
            <input type="hidden" name="file" value="icon">
            <input type="hidden" name="idPost" value="' . $user['login'] . '">
            <button type="submit" class="btn" name="submit">Změnit ikonku</button></div>
        </div> 
    </form>
    </div>
     ';

    //změna jména
    echo '<h4>Změna jména</h4>';
    echo '<form class="form-horizontal" action="" method="POST">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Nové jméno:</label>
                    <div class="col-sm-3">
                       <input type="text" class="form-control" name="name" placeholder="Jméno">
                    </div>
                </div>
                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-7">
                        <input type="hidden" name="log" value="changeName">
                        <input class="btn" type="submit" name="submit" value="Uložit">
                    </div>
                </div>
            </form>';


    //změna hesla
    echo '<h4>Změna hesla</h4>';
    echo '<form class="form-horizontal" action="" method="POST">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pas1">Staré heslo:</label>
                    <div class="col-sm-3">
                       <input type="password" class="form-control" name="pass1" placeholder="Staré heslo">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pass2">Nové heslo:</label>
                    <div class="col-sm-3"> 
                        <input type="password" class="form-control" name="pass2" placeholder="Nové heslo">
                    </div>
                </div>
                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-7">
                        <input type="hidden" name="log" value="changePass">
                        <input class="btn" type="submit" name="submit" value="Uložit">
                    </div>
                </div>
            </form>';

    //změna emailu
    echo '<h4>Změna e-mailu</h4>';
    echo '<form class="form-horizontal" action="" method="POST">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="email">Nový e-mail:</label>
                    <div class="col-sm-3">
                       <input type="email" class="form-control" name="email" placeholder="E-mail">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pass2">Heslo:</label>
                    <div class="col-sm-3"> 
                        <input type="password" class="form-control" name="password" placeholder="Heslo">
                    </div>
                </div>
                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-7">
                        <input type="hidden" name="log" value="changeMail">
                        <input class="btn" type="submit" name="submit" value="Uložit">
                    </div>
                </div>
            </form>';

//pro nepřihlášené uživatele    
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';