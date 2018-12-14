<?php

$allUsers = $params['db']->allUsers();
$roles = $params['db']->allRights();

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

//pro přihlášené uživatele
if ($user != null) {
    
    if ($allUsers != null) {
        foreach ($allUsers as $index) {
            echo '<div class="container-fluid">';
                echo '<div class="well well-sm well-top">';
                
                    if ( ($params["user"]["role"]==1) && ($params["user"]["login"] != $index['login']) ) {
                        echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="log" value="delete">
                                <input type="hidden" name="loginUser" value="'.$index['login'].'">
                                <button type="submit" class="linkButton" name="submit"><span class="glyphicon glyphicon-trash"></span></button>
                            </form>
                            ';
                    }
                    echo $index['login'];
                echo '</div>';
                echo '<div class="well well-bottom well-member">';
                    echo '<div class ="img"><img src="../img/empty.png" width="70px" height="90px" ></div>';
                    echo 'Jméno: '.$index['jmeno'].'<br>';

                    if ( ($params["user"]["role"]==1) && ($params["user"]["login"] != $index['login']) ) {
                        echo '
                                <form class="form-inline" action="" method="POST">
                                    Role:
                                <div class="form-group">
                                <select  id="right" name="right">';
                        foreach ($roles as $role) {
                                if ($index['role']['id'] == $role['id']) {
                                    echo '<option value="'.$role['id'].'" selected>' . $role['nazev'] . '</option>';
                                } else {
                                    echo '<option value="'.$role['id'].'">' . $role['nazev'] . '</option>';
                                }
                        }
                        echo '</select>
                            </div>

                            <input type="hidden" name="log" value="setRight">
                            <input type="hidden" name="login" value="' . $index['login'] . '">
                            <button type="submit" class="" name="submit">Set</span></button>
                        </form>
                        </br>    
                         ';
                    } else {
                        echo 'Role: ';
                        echo $index['roleData']['nazev'];
                    }
           
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