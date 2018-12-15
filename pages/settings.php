<?php

echo '<div class="container-fluid">';

if (isset($params["error"])) {
    echo '<div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Chyba!</strong> '.$params["error"].'
          </div>';
    unset($params["error"]);
} else if (isset($params["message"])) {
    echo '<div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Úspěch!</strong> '.$params["message"].'
          </div>';
    unset($params["message"]);
}

if ($user != null) {
echo '<h3>Nastavení</h3>';


echo '
    <div class="row">
    <div class="col-sm-3">
    ikonka
    </div>
    <div class="col-sm-3">
    <table class="table">
        <tbody>
            <tr>
                <td>Login</td>
                <td>'.$params['user']['login'].'</td>
            </tr>
            <tr>
                <td>Jméno</td>
                <td>'.$params['user']['jmeno'].'</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>'.$params['user']['email'].'</td>
            </tr>
            <tr> <td></td> <td></td> </tr>
        </tbody>
    </table>
    </div>
    </div>
        ';    


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

} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';