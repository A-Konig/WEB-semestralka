<?php

/**
 * Stránka s vypsanými členy. Zobsazuje se jejich jméno, login a ikonka.
 * Admin má dále možnost členy zablokovat nebo smazat, či změnit jejich přístupová práva.
 * Dále má admin možonst zobrazit pouze zablokované členy, které může buď odblokovat nebo je smazat.
 * 
 */

$allUsers = $params['db']->allUsers();
$roles = $params['db']->allRights();

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

//stránkování
$pg = 1;
if (isset($_GET['pg']) && filter_var($_GET['pg'], FILTER_VALIDATE_INT)) {
    $pg = $_GET['pg'];
}

//pro přihlášené uživatele
if ($user != null) {

    //zablokovaní členové
    if (isset($_GET['bl']) && $_GET['bl'] == 1 ) {
        if ($user['role'] == 1) {
            echo '<div class="posts">';
            echo '<h4>Zablokovaní uživatelé</h4>';

            $i = 0;
            if ($allUsers != null) {
                foreach ($allUsers as $index) {
                    if ($index['block'] == 1) {
                        $i++;
                    }
                }
            }

            //volba stránky
            if (($pg * 5) > ($i + 4)) {
                $pg = 1;
            }

            //vypisování uživatelů
            $i = 0;
            if ($allUsers != null) {
                foreach ($allUsers as $index) {
                    if ($index['block'] == 1) {
                        $i++;
                        
                        //pokud patří na tuto stránku
                        if (($i >= ($pg - 1) * 6) && ($i < ($pg) * 6)) {
                            echo '<div class="container-fluid">';
                            echo '<div class="well well-sm well-top">';

                            //smazání
                            if (($params["user"]["role"] == 1) && ($params["user"]["login"] != $index['login'])) {
                                echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="log" value="delete">
                                <input type="hidden" name="loginUser" value="' . $index['login'] . '">
                                <button type="submit" class="linkButton" name="submit"><span class="glyphicon glyphicon-trash"></span></button>
                            </form>
                            ';

                                echo '<span class="glyphicon glyphicon-none floatright"></span>';

                            //unblock    
                            echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="log" value="unblock">
                                <input type="hidden" name="loginUser" value="' . $index['login'] . '">
                                <button type="submit" class="linkButton" name="submit"><span class="glyphicon glyphicon-ok-sign"></span></button>
                            </form>
                            ';
                            }

                            echo $index['login'];
                            echo '</div>';
                            echo '<div class="well well-bottom well-member">';

                            //ikonka
                            $file = 'img/' . $index['ikonka'];
                            if (file_exists($file)) {
                                echo '<div class ="img"><img src="img/' . $index['ikonka'] . '" width="70px" height="90px" ></div>';
                            } else {
                                echo '<div class ="img"><img src="img/empty.png" width="70px" height="90px" ></div>';
                            }

                            if ($index['jmeno'] != null) {
                                echo 'Jméno: ' . $index['jmeno'] . '<br>';
                            }

                            //změna role
                            if (($params["user"]["role"] == 1) && ($params["user"]["login"] != $index['login'])) {
                                echo '
                                <form class="form-inline" action="" method="POST">
                                    Role:
                                <div class="form-group">
                                <select  id="right" name="right">';
                                foreach ($roles as $role) {
                                    if ($index['role']['id'] == $role['id']) {
                                        echo '<option value="' . $role['id'] . '" selected>' . $role['nazev'] . '</option>';
                                    } else {
                                        echo '<option value="' . $role['id'] . '">' . $role['nazev'] . '</option>';
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
                }
            }

            if ($i == 0) {
                echo 'Žádní zablokovaní uživatelé';
            }

            //šipky pro změnu stránky
            echo '<div class="col-md-offset-5">';
            if ($pg > 1) {
                echo '<a class="btn" href="/index.php?page=members&bl=1&pg=' . ($pg - 1) . '">
                    <<</button>
                    </a>
                     ';
            }

            if ($i > $pg * 5) {
                echo '<a class="btn" href="/index.php?page=members&bl=1&pg=' . ($pg + 1) . '">
                    >></button>
                    </a>
                     ';
            }
            echo '</div>';

            echo '</div>';
        } else {
            echo '<h2><span class="glyphicon glyphicon-remove"></span> Nedostatečné oprávnění</h2>';
        }

        //ostatní členové    
    } else {

        //odkaz na zablokované uživatele
        if ($user['role'] == 1) {
            echo '<span class="floatright">';
            echo '<a href="index.php?page=members&bl=1"><button type="button" class="btn">Zablokovaní uživatelé</button></a>';
            echo '</span>';
        }


        if ($allUsers != null) {
            echo '<div class="posts">';

            $i = 0;
            foreach ($allUsers as $index) {
                if ($index['block'] == 0) {
                    $i++;
                }
            }

            //volba stránky
            if (($pg * 5) > ($i + 4)) {
                $pg = 1;
            }

            //výpis členů
            $i = 0;
            foreach ($allUsers as $index) {
                if ($index['block'] == 0) {
                    $i++;
                    if (($i >= ($pg - 1) * 6) && ($i < ($pg) * 6)) {
                        echo '<div class="container-fluid">';
                        echo '<div class="well well-sm well-top">';

                        //možnosti pro admina
                        if (($params["user"]["role"] == 1) && ($params["user"]["login"] != $index['login'])) {
                          //smazání
                            echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="log" value="delete">
                                <input type="hidden" name="loginUser" value="' . $index['login'] . '">
                                <button type="submit" class="linkButton" name="submit"><span class="glyphicon glyphicon-trash"></span></button>
                            </form>
                            ';

                            echo '<span class="glyphicon glyphicon-none floatright"></span>';

                            //block    
                            echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="log" value="block">
                                <input type="hidden" name="loginUser" value="' . $index['login'] . '">
                                <button type="submit" class="linkButton" name="submit"><span class="glyphicon glyphicon-minus-sign"></span></button>
                            </form>
                            ';
                        }

                        echo $index['login'];
                        echo '</div>';
                        echo '<div class="well well-bottom well-member">';

                        //ikonka
                        $file = 'img/' . $index['ikonka'];
                        if (file_exists($file)) {
                            echo '<div class ="img"><img src="img/' . $index['ikonka'] . '" width="70px" height="90px" ></div>';
                        } else {
                            echo '<div class ="img"><img src="img/empty.png" width="70px" height="90px" ></div>';
                        }


                        if ($index['jmeno'] != null) {
                            echo 'Jméno: ' . $index['jmeno'] . '<br>';
                        }

                        //změna role
                        if (($params["user"]["role"] == 1) && ($params["user"]["login"] != $index['login'])) {
                            echo '
                                <form class="form-inline" action="" method="POST">
                                    Role:
                                <div class="form-group">
                                <select  id="right" name="right">';
                            foreach ($roles as $role) {
                                if ($index['role']['id'] == $role['id']) {
                                    echo '<option value="' . $role['id'] . '" selected>' . $role['nazev'] . '</option>';
                                } else {
                                    echo '<option value="' . $role['id'] . '">' . $role['nazev'] . '</option>';
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
            }

            //šipky pro změnu stránky
            echo '<div class="col-md-offset-5">';
            if ($pg > 1) {
                echo '<a class="btn" href="/index.php?page=members&pg=' . ($pg - 1) . '">
                    <<
                    </a>
                     ';
            }

            if ($i > $pg * 5) {
                echo '<a class="btn" href="/index.php?page=members&pg=' . ($pg + 1) . '">
                    >>
                    </a>
                     ';
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
