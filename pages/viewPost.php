<?php

/**
 * Stránka zobrazující vybraný příspěvek.
 * Pokud je uživatel nepřihlášený, vidí jen text schválených článků a přiložený soubor.
 * Přihlášený uživatel vidí i recenze pod článkem.
 * Pokud je přihlášený uživatel adminem, vidí i neschválené příspěvkya jejich recenze a může je zamítnout či schválit.
 * Pokud je přihlášeným uživatelem autor článku, vidí článek i pokud nebyl dosud schválen. Zároveň má následující možnosti:
 *  článek smazat
 *  článek editovat
 * Pokud je přihlášeným uživatelem recenzent, kterému byl přiřazen článek k recenzi, má pod článkem možnost přidat recenzi.
 * Pokud již recenzi napsal, může ji upravovat.
 */
echo '<div class="container-fluid">';

//výpisy výsledku odeslání formuláře
if (isset($params["error"])) {
    echo '<div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Chyba!</strong> ' . $params["error"] . '
          </div>';
    unset($params["error"]);
} else if (isset($params["message"])) {
    echo '<div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Úspěch!</strong> ' . $params["message"] . '
          </div>';
    unset($params["message"]);
}

//výběr článku
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $post = $params['db']->getPost($_GET['id']);

    //existuje příspěvek k zobrazení
    if (($post != null) && ( ($post['schvaleny'] == 1) || ($params["user"]["role"] == 1) || ($params['user']['login'] == $post['autor']) ||
            (($post['rec1'] == $params["user"]["login"]) || ($post['rec2'] == $params["user"]["login"]) || ($post['rec3'] == $params["user"]["login"])) )) {

        $recs = $params['db']->getRecs($_GET['id']);

        //možnosti pro autora
        if (($params['user']['login'] == $post['autor']) && ( ($post['schvaleny'] == 0) || ($post['schvaleny'] == -1))) {
            //editování příspěvku
            echo '<a class="floatright btn controlBtn" href="/index.php?page=editPage&idp=' . $post['id'] . '">
                    <span class="glyphicon glyphicon-pencil"></span> Editovat</button>
                    </a>
                     ';
            //smazání příspěvku
            echo '
                  <div class="floatright">
                    <span class="glyphicon glyphicon-none"></span>
                  </div>
                  <div class="floatright">
                  <form class="form-inline" action="" method="POST">
                       <input type="hidden" name="post" value="delete">
                       <input type="hidden" name="idPost" value="' . $post['id'] . '">
                       <button type="submit" class="btn controlBtn" name="submit"> <span class="glyphicon glyphicon-trash"></span>Smazat</button>
                  </form>
                  </div>';
        }

        echo '<h3>' . $post['nazev'] . '</h3>';
        echo '<b>Autor: </b>' . $post['autor'] . '';
        echo '<div class="floatright">' . $post['datum'] . '</div>';

        $obsah = str_replace("\n", "<br>", $post['obsah']);

        echo '<div class="text"><p>' . $obsah . '</p></div>';

        //odkaz na soubor
        if ($post['file'] != null) {
            $file = 'files/' . $post['file'];

            if (file_exists($file)) {
                echo '<br>';
                echo '<b><a href="files/' . $post['file'] . '"><span class="glyphicon glyphicon-file"></span>  Zobrazit přiložený soubor</a></b>';

                //pro autora možnost soubor smazat
                if (($params['user']['login'] == $post['autor']) && ( ($post['schvaleny'] == 0) || ($post['schvaleny'] == -1))) {
                    echo '
                            <form class="form-inline" action="" method="POST">
                                <input type="hidden" name="file" value="delete">
                                <input type="hidden" name="idPost" value="' . $post['id'] . '">
                                <button type="submit" class="btn controlBtn" name="submit">Odstranit soubor</button>
                            </form>
                               ';
                }
            }
        }

        //recenze
        if ($params["user"] != null) {
            if ($recs != null) {
                echo '<hr class="breakpoint">';
                echo '<h4>Recenze:</h4>';

                foreach ($recs as $rec) {
                    echo '<div class="container-fluid">';
                    echo '<div class="well well-sm well-top">';
                    if ($rec['aktualni'] == 0) {
                        echo '<span class="label label-warning">Staré</span> <span class="glyphicon glyphicon-none"></span>';
                    }
                    echo $rec['autor'];


                    if ($params["user"] != null) {
                        //pro autora upravování
                        if ($params["user"]["login"] == $rec['autor'] && $post['schvaleny'] == 0) {
                            echo '<a class="floatright" href="/index.php?page=editPage&idr=' . $rec['id'] . '">
                        <span class="glyphicon glyphicon-pencil"></span></button>
                        </a>
                         ';
                        }

                        //pro admina smazání
                        if ($params["user"]["role"] == 1) {
                            echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="rec" value="delete">
                                <input type="hidden" name="idRec" value="' . $rec['id'] . '">
                                <button type="submit" class="linkButton" name="submit"> <span class="glyphicon glyphicon-trash"></span></button>
                            </form>
                            ';
                        }
                    }

                    //celkové hodnocení
                    echo '<div class="floatright">';
                    for ($i = 0; $i < $rec['celkove']; $i++) {
                        echo '<span class="glyphicon glyphicon-star"></span> ';
                    }
                    for ($l = 0; $l < (5 - $rec['celkove']); $l++) {
                        echo '<span class="glyphicon glyphicon-star-empty"></span>';
                    }
                    echo '</div>';

                    echo '</div>';
                    echo '<div class="well well-bottom">';

                    //hodnocení jazyka
                    echo '<div class="floatright rightAlign">';
                    echo 'Jazyk: ';
                    for ($i = 0; $i < $rec['jazyk']; $i++) {
                        echo '<span class="glyphicon glyphicon-star"></span>';
                    }
                    for ($l = 0; $l < (5 - $rec['jazyk']); $l++) {
                        echo '<span class="glyphicon glyphicon-star-empty"></span>';
                    }
                    echo '<br>';

                    //hodnocení originality
                    echo 'Originalita:';
                    for ($i = 0; $i < $rec['originalita']; $i++) {
                        echo '<span class="glyphicon glyphicon-star"></span>';
                    }
                    for ($l = 0; $l < (5 - $rec['originalita']); $l++) {
                        echo '<span class="glyphicon glyphicon-star-empty"></span>';
                    }
                    echo '</div>';


                    echo '<div>' . $rec['datum'] . '</div>';
                    echo '<br>';
                    $obsahR = str_replace("\n", "<br>", $rec['obsah']);

                    echo $obsahR;
                    echo '</div>';
                    echo '</div>';
                }
            }

            $isRew = 0;
            $allRecs = $params['db']->getRecs($post['id']);
            if ($allRecs != null) {
                foreach ($allRecs as $rec) {
                    if ($rec['autor'] == $params['user']['login']) {
                        $isRew = 1;
                    }
                }
            }

            //přidání nové recenze
            if (($isRew == 0) && ( ($user["login"] == $post["rec1"]) || ($user["login"] == $post["rec2"]) || ($user["login"] == $post["rec3"]) )) {
                echo '<br>';
                echo '<h4>Přidat recenzi:</h4>';

                echo '<form class="form-horizontal" action="" method="POST"">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="content">Obsah:</label>
                            <div class="col-sm-10"> 
                                <textarea class="form-control" rows="10" name="content" placeholder="Text recenze" required></textarea>
                            </div>
                        </div>';

                //hodnocení originality      ¨
                echo '<div class="form-group">
                            <label class="control-label col-sm-2" for="orig">Originalita</label>
                        ';
                for ($i = 1; $i < 6; $i++) {
                    echo '<label class="radio-inline"><input type="radio" name="orig" value="' . $i . '">';
                    for ($j = 0; $j < $i; $j++) {
                        echo '<span class="glyphicon glyphicon-star-empty"></span>';
                    }
                    echo '</label>';
                }
                echo '</div>';

                //hodnocení jazyka      
                echo '<div class="form-group">
                            <label class="control-label col-sm-2" for="lang">Jazyk</label>
                        ';
                for ($i = 1; $i < 6; $i++) {
                    echo '<label class="radio-inline"><input type="radio" name="lang" value="' . $i . '">';
                    for ($j = 0; $j < $i; $j++) {
                        echo '<span class="glyphicon glyphicon-star-empty"></span>';
                    }
                    echo '</label>';
                }
                echo '</div>';

                //celkové hodnocení      
                echo '<div class="form-group">
                            <label class="control-label col-sm-2" for="summary">Celkově</label>
                        ';
                for ($i = 1; $i < 6; $i++) {
                    echo '<label class="radio-inline"><input type="radio" name="summary" value="' . $i . '">';
                    for ($j = 0; $j < $i; $j++) {
                        echo '<span class="glyphicon glyphicon-star-empty"></span>';
                    }
                    echo '</label>';
                }
                echo '</div>';

                echo '     </div>
                        <div class="form-group"> 
                            <div class="col-sm-offset-2 col-sm-9">
                                <input type="hidden" name="idPost" value="' . $_GET['id'] . '">
                                <input type="hidden" name="rec" value="newRec">
                                <input class="btn" type="submit" name="submit" value="Publikovat">
                            </div>
                        </div>
                    </form>';
            }

            //pro admina
            if (($params["user"]["role"] == 1)) {

                if ($post['schvaleny'] == 0) {

                    //odmítnutí příspěvku
                    echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="post" value="deny">
                                <input type="hidden" name="idPost" value="' . $post['id'] . '">
                                <button type="submit" class="btn controlBtn" name="submit"> <span class="glyphicon glyphicon-remove remPost"></span></button>
                            </form>
                              ';


                    echo '<div class="floatright extendLink"></div>';

                    //schválení příspěvku, pokud je splněna podmínka 3 recenzí
                    $recsPost = $params['db']->getRecs($_GET['id']);
                    if (isset($recsPost)) {
                        $num = count($recsPost);
                    }

                    if ($num >= 3) {
                        echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="post" value="publish">
                                <input type="hidden" name="idPost" value="' . $post['id'] . '">
                                <button type="submit" class="btn controlBtn" name="submit"> <span class="glyphicon glyphicon-ok"></span></button>
                            </form>
                             ';
                    }
                }
            }
        }


        //neexistuje příspěvek k zobrazeníí    
    } else {
        echo '<h2><span class="glyphicon glyphicon-remove"></span> Nic k zobrazení</h2>';
    }

//není zadán příspěvek k zobrazení    
} else {
    echo '<h2><span class="glyphicon glyphicon-remove"></span> Nic k zobrazení</h2>';
}

echo '</div>';
