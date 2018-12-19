<?php

/**
 * Stránka dostupná uživatelům s přístupovými právy autora.
 * Nachází se na ní výpis příspěvků rozdělený do tří částí:
 *  zamítnuté příspěvky
 *  příspěvky čekající na schválení
 *  publikované příspěvky
 * Zamítnuté příspěvky má autor možnost změnít a znovu je zařadit do procesu schvalování.
 * Příspěvky čekající na schválení může měnít. Oba druhy příspěvků může libovolně mazat.
 * Publikované příspěvky už může jen prohlížet.
 * 
 */
$allPosts = $params['db']->allPosts();

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

//je přihlášen? a je to autor?
if (isset($params["user"])) {
    if ($params["user"]["role"] == 3) {

        //odkaz na vytvoření nového příspěvku
        echo '<span class="floatright">';
        echo '<a href="index.php?page=newPost"><button type="button" class="btn">Nový příspěvek</button></a> ';
        echo '</span>';

        //zamítnuté příspěvky
        echo '<br>';
        echo '<h4>Zamítnuté</h4>';
        $i = 0;
        if ($allPosts != null) {
            foreach ($allPosts as $post) {
                if (($post['autor'] == $user["login"]) && ($post['schvaleny'] == -1)) {
                    $i++;
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

                    //edit příspěvku
                    echo '<a class="floatright" href="/index.php?page=editPage&idp=' . $post['id'] . '">
                    <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                     ';

                    echo '<span class="glyphicon glyphicon-none floatright"></span>';

                    //hodnocení
                    echo '<div class="floatright">';
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
                    echo '</div>';

                    //odkaz na zobrazení příspěvku
                    echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>"
                    . $post['nazev'] . "</span></a>";
                    echo '</div>';

                    echo "<div class='well well-bottom'>";

                    echo $post['autor'] . "<br>";
                    //pokud byl k příspěvku přiložen soubor, vypíše se na něj odkaz
                    if ($post['file'] != null) {
                        $file = 'files/' . $post['file'];

                        if (file_exists($file)) {
                            echo '<a href="' . $file . '"><span class="glyphicon glyphicon-file"></span>  Zobrazit přiložený soubor</a>';
                        }
                    }
                    echo '</div>';

                    echo '</div>';
                }
            }
        }
        if ($i == 0) {
            echo 'Žádné zamítnuté příspěvky';
        }
        echo '<hr>';

        //příspěvky čekající na schválení
        echo '<h4>Čekající na schválení</h4>';
        $i = 0;
        if ($allPosts != null) {
            foreach ($allPosts as $post) {

                if (($post['autor'] == $user["login"]) && ($post['schvaleny'] == 0)) {
                    $i++;
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

                    //edit příspěvku
                    echo '<a class="floatright" href="/index.php?page=editPage&idp=' . $post['id'] . '">
                    <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                     ';

                    echo '<span class="glyphicon glyphicon-none floatright"></span>';

                    //hodnocení
                    echo '<div class="floatright">';
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
                    echo '</div>';

                    //odkaz na zobrazení příspěvku
                    echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>"
                    . $post['nazev'] . "</span></a>";
                    echo '</div>';


                    echo "<div class='well well-bottom'>";

                    echo '<div class="floatright">' . $post['datum'] . '</div>';
                    echo $post['autor'] . "<br>";
                    //pokud byl k příspěvku přiložen soubor, zobrazí se na něj odkaz
                    if ($post['file'] != null) {
                        $file = 'files/' . $post['file'];

                        if (file_exists($file)) {
                            echo '<a href="' . $file . '"><span class="glyphicon glyphicon-file"></span>  Zobrazit přiložený soubor</a>';
                        }
                    }
                    echo '</div>';

                    echo '</div>';
                }
            }
        }
        if ($i == 0) {
            echo 'Žádné příspěvky čekající na schválení';
        }
        echo '<hr>';

        //publikované příspěvky
        echo '<h4>Publikované</h4>';

        $i = 0;
        if ($allPosts != null) {
            foreach ($allPosts as $index) {
                if (($post['autor'] == $params["user"]["login"]) && ($post['schvaleny'] == 1)) {
                    $i++;
                }
            }
        }

        //výběr stránky
        if (($pg * 5) > ($i + 4)) {
            $pg = 1;
        }

        $i = 0;
        if ($allPosts != null) {
            foreach ($allPosts as $post) {
                if (($post['autor'] == $user["login"]) && ($post['schvaleny'] == 1)) {
                    $i++;
                    if (($i >= ($pg - 1) * 6) && ($i < ($pg) * 6)) {
                        echo '<div class="posts">';


                        echo '<div class="well well-sm well-top">';

                        //hodnocení
                        echo '<div class="floatright">';
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
                        echo '</div>';

                        //odkaz na zobrazení článku
                        echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>"
                        . $post['nazev'] . "</span></a>";
                        echo '</div>';

                        echo "<div class='well well-bottom'>";

                        echo '<div class="floatright">' . $index['datum'] . '</div>';

                        echo $post['autor'] . "<br>";
                        if ($post['file'] != null) {
                            $file = 'files/' . $post['file'];

                            if (file_exists($file)) {
                                echo '<a href="' . $file . '"><span class="glyphicon glyphicon-file"></span>  Zobrazit přiložený soubor</a>';
                            }
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
        }
        if ($i == 0) {
            echo 'Žádné publikované příspěvky';
        }

        //šipky pro pohyb mezi stránkami
        echo '<div class="col-md-offset-5">';
        if ($pg > 1) {
            echo '<a class="btn" href="/index.php?page=myPosts&pg=' . ($pg - 1) . '">
                    <<
                    </a>
                     ';
        }

        if ($i > $pg * 5) {
            echo '<a class="btn" href="/index.php?page=myPosts&pg=' . ($pg + 1) . '">
                    >>
                    </a>
                     ';
        }
        echo '</div>';
        
    } else {
        echo '<h2><span class="glyphicon glyphicon-remove"></span> Nedostatečné oprávnění</h2>';
    }
    
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';

