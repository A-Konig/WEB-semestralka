<?php

echo '<div class="container-fluid">';

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

if (isset($_GET['id'])) {
    $post = $params['db']->getPost($_GET['id']);

    //existuje příspěvek k zobrazení
    if (($post != null) && ( ($post['schvaleny'] == 1) || ($params["user"]["role"] == 1) || ($params['user']['login'] == $post['autor']) ||
            (($post['rec1'] == $params["user"]["login"]) || ($post['rec2'] == $params["user"]["login"]) || ($post['rec3'] == $params["user"]["login"])) )) {

        $recs = $params['db']->getRecs($_GET['id']);

        //editování postu
        if (($params['user']['login'] == $post['autor']) && ($post['schvaleny'] == 0)) {
            echo '<a class="floatright btn" href="/index.php?page=editPage&idp=' . $post['id'] . '">
                    <span class="glyphicon glyphicon-pencil"></span> Editovat</button>
                    </a>
                     ';
        }

        echo '<h3>' . $post['nazev'] . '</h3>';
        echo '<b>Autor: </b>' . $post['autor'] . '';
        echo '<div class="floatright">' . $post['datum'] . '</div>';
        echo '<div class="text"><p>' . $post['obsah'] . '</p></div>';

        //hodnocení
        if ($recs != null) {
            echo '<hr class="breakpoint">';
            echo '<h4>Recenze:</h4>';

            foreach ($recs as $rec) {
                echo '<div class="container-fluid">';
                echo '<div class="well well-sm well-top">';
                echo $rec['autor'];

                //pro admina - mazání recenze
                if ($params["user"] != null) {
                    if ($params["user"]["login"] == $rec['autor']) {
                        echo '<a class="floatright" href="/index.php?page=editPage&idr=' . $rec['id'] . '">
                        <span class="glyphicon glyphicon-pencil"></span></button>
                        </a>
                         ';
                    }

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
                    echo '<span class="glyphicon glyphicon-star-empty"></span> ';
                }
                echo '</div>';

                echo '</div>';
                echo '<div class="well well-bottom">';

                echo '<div class="floatright">' . $rec['datum'] . '</div>';

                //hodnocení jazyka
                echo '<div>';
                echo 'Jazyk: ';
                for ($i = 0; $i < $rec['jazyk']; $i++) {
                    echo '<span class="glyphicon glyphicon-star-empty"></span> ';
                }
                echo '</div>';

                //hodnocení originality
                echo '<div>';
                echo 'Originalita:';
                for ($i = 0; $i < $rec['originalita']; $i++) {
                    echo '<span class="glyphicon glyphicon-star-empty"></span> ';
                }
                echo '</div>';


                echo $rec['obsah'];
                echo '</div>';
                echo '</div>';
            }
        }

        if ($params["user"] != null) {

            $isRew = 0;
            $allRecs = $params['db']->getRecs($post['id']);
            if ($allRecs != null) {
                foreach ($allRecs as $rec) {
                    if ($rec['autor'] == $params['user']['login']) {
                        $isRew = 1;
                    }
                }
            }

            //přidání nového
            if (($isRew == 0) && ( ($user["login"] == $post["rec1"]) || ($user["login"] == $post["rec2"]) || ($user["login"] == $post["rec3"]) )) {
                echo '<br>';
                echo '<h4>Přidat recenzi:</h4>';

                if ($user['block'] == 1) {
                    echo '<h2><span class="glyphicon glyphicon-remove"></span> Nedostatečné oprávnění </h2>';
                } else {
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
            }

            if (($params["user"]["role"] == 1)) {
                //mazání příspěvku
                echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="post" value="delete">
                                <input type="hidden" name="idPost" value="' . $post['id'] . '">
                                <button type="submit" class="btn" name="submit"> <span class="glyphicon glyphicon-trash"></span></button>
                            </form>
                              ';


                if ($post['schvaleny'] == 0) {

                    echo '<div class="floatright extendLink"></div>';

                    //schválení příspěvku
                    echo '
                                <form class="form-inline floatright" action="" method="POST">
                                    <input type="hidden" name="post" value="publish">
                                    <input type="hidden" name="idPost" value="' . $post['id'] . '">
                                    <button type="submit" class="btn" name="submit"> <span class="glyphicon glyphicon-ok"></span></button>
                                </form>
                             ';
                }
            }
        }


        //neexistuje příspěvek k zobrazeníí    
    } else {
        echo 'Nothing to see here.';
    }

//není zadán příspěvek k zobrazení    
} else {
    echo 'Nothing to see here.';
}

echo '</div>';
