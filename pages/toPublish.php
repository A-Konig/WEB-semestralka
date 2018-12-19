<?php

/**
 * Stránka přístupná pouze administrátorovi.
 * Zde může vidět neschválené příspěvky. U každého se zobrazuje jeho dosavadní hodnocení a datum publikace.
 * Dále může administrátor ke každému příspěvku přiřadit až 3 recenzenty najednou. Recenzenty může opět odebrat, anebo po tom, co oni příspěvek ohodnotí, jsou automaticky odebráni.
 * Není možné aby jeden recenzent hodnotil ten samý článek víckrát.
 * Dále je zde možnost příspěvek zamítnout či smazat. Pokud je splněn limit minimálně tří recenzi, je zde i možnost příspěvek schválit.
 * Tímto se příspěvek stává dostupný veřejnosti.
 * 
 */
$posts = $params['db']->allPosts();
$recs = $params['db']->allRecs();
$allUsers = $params['db']->allUsers();

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
if ($params["user"] != null) {
    if ($params["user"]["role"] == 1) {

        //stránkování
        $pg = 1;
        if (isset($_GET['pg']) && filter_var($_GET['pg'], FILTER_VALIDATE_INT)) {
            $pg = $_GET['pg'];
        }

        //výpis příspěvků
        echo '<h3>Příspěvky</h3>';
        if ($posts == null) {
            echo '<p>Žádné příspěvky čekající na schválení</p>';
        } else {

            $numP = 0;
            foreach ($posts as $index) {
                if ($index['schvaleny'] == 0) {
                    $numP++;
                }
            }

            //výběr stránky
            if (($pg * 5) > ($numP + 4)) {
                $pg = 1;
            }

            $numP = 0;
            foreach ($posts as $post) {
                if ($post['schvaleny'] == 0) {
                    $numP++;
                    if (($numP >= ($pg - 1) * 6) && ($numP < ($pg) * 6)) {
                        $recsPost = $params['db']->getRecs($post['id']);

                        echo '<div class="container-fluid">';
                        echo '<div class="posts">';

                        echo '<div class="well well-sm well-top">';

                        //smazání příspěvku
                        echo '
                        <form class="form-inline floatright" action="" method="POST">
                            <input type="hidden" name="post" value="delete">
                            <input type="hidden" name="idPost" value="' . $post['id'] . '">
                            <button type="submit" class="linkButton" name="submit"> <span class="glyphicon glyphicon-trash"></span></button>
                        </form>
                          ';

                        echo '<span class="glyphicon glyphicon-none floatright"></span>';

                        //zamítnutí příspěvku
                        echo '
                        <form class="form-inline floatright" action="" method="POST">
                            <input type="hidden" name="post" value="deny">
                            <input type="hidden" name="idPost" value="' . $post['id'] . '">
                            <button type="submit" class="linkButton" name="submit"> <span class="glyphicon glyphicon-remove remPost"></span></button>
                        </form>
                          ';

                        echo '<span class="glyphicon glyphicon-none floatright"></span>';

                        //schválení příspěvku
                        $notAc = false;
                        if (isset($recsPost)) {
                            $num = count($recsPost);


                            foreach ($recsPost as $recP) {
                                if ($recP['aktualni'] == 0) {
                                    $notAc = true;
                                }
                            }
                        }

                        if ($num >= 3) {
                            echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="post" value="publish">
                                <input type="hidden" name="idPost" value="' . $post['id'] . '">
                                <button type="submit" class="linkButton" name="submit"> <span class="glyphicon glyphicon-ok"></span></button>
                            </form>
                             ';
                        }

                        //počet recenzí
                        echo '<div class="floatright">';
                        echo $num . '/3';
                        echo '</div>';

                        //pokud je některá z recenzí ze staré verze článku (došlo mezitím k editu)
                        if ($notAc) {
                            echo '<div class="floatright">';
                            echo '<span class="label label-warning">Staré</span> <span class="glyphicon glyphicon-none"></span>';
                            echo '</div>';
                        }

                        //odkaz na zobrazení článku
                        echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>"
                        . $post['nazev'] . "</span></a>";

                        echo "</div>";

                        echo "<div class='well well-bottom well-padd'>";


                        //přiřazení recenzentů
                        echo '<div class="floatright">';

                        echo '<form class="" action="" method="POST">';

                        for ($i = 1; $i < 4; $i++) {
                            echo '
                                
                                <select  id="rec" name="rec' . $i . '">
                                    <option></option>';
                            if ($allUsers != null) {
                                foreach ($allUsers as $user) {
                                    $ok = 1;
                                    //už napsal recenzi
                                    if (isset($recsPost)) {
                                        foreach ($recsPost as $postRec) {
                                            if ($postRec['autor'] == $user['login']) {
                                                $ok = 0;
                                            }
                                        }
                                    }

                                    if ($user['role'] == 2 && $user['block'] == 0 && $ok == 1) {
                                        //už je zvolen
                                        if ($user[login] == $post['rec' . $i]) {
                                            echo '<option selected>' . $user['login'] . '</option>';
                                        } else {
                                            echo '<option>' . $user['login'] . '</option>';
                                        }
                                    }
                                }
                            }
                            echo '</select><br>';
                        }

                        echo '<input type="hidden" name="post" value="setRec">
                                    <input type="hidden" name="idPost" value="' . $post['id'] . '">
                                    <input class="submitbutton" type="submit" name="submit" value="Uložit">
                             </form>';

                        echo '</div>';
                        echo '<div class="floatright">Recenzenti:<span class="glyphicon glyphicon-none"></span></div>';

                        //hodnocení
                        echo 'Hodnocení: ';
                        $value = 0;
                        $outOf = 0;
                        $recs = $params['db']->getRecs($post['id']);
                        if ($recs != null) {
                            foreach ($recs as $rec) {
                                $outOf += 5;
                                $value += $rec['celkove'];
                            }
                        }
                        echo $value . ' / ' . $outOf;
                        echo '<br>';

                        echo $post['autor'] . "<br>";
                        //pokud je k příspěvku přiřazen soubor, vypíše se na něj odkaz
                        if ($post['file'] != null) {
                            $file = 'files/' . $post['file'];

                            if (file_exists($file)) {
                                echo '<a href="' . $file . '"><span class="glyphicon glyphicon-file"></span>  Zobrazit přiložený soubor</a>';
                            }
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
            if ($numP == 0) {
                echo '<p>Žádné příspěvky čekající na schválení</p>';
            }

            //šipky na změnu aktuální stránky
            echo '<div class="col-md-offset-5">';
            if ($pg > 1) {
                echo '<a class="btn" href="/index.php?page=toPublish&pg=' . ($pg - 1) . '">
                    <<</button>
                    </a>
                     ';
            }

            if ($numP > $pg * 5) {
                echo '<a class="btn" href="/index.php?page=toPublish&pg=' . ($pg + 1) . '">
                    >></button>
                    </a>
                     ';
            }
            echo '</div>';
        }
    } else {
        echo '<h2><span class="glyphicon glyphicon-remove"></span> Nedostatečné oprávnění</h2>';
    }

//pro nepřihlášené uživatele    
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';
