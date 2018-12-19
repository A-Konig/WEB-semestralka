<?php

/**
 * Stránka přístupná uživatelům s přístupovými právy recenzenta.
 * Zde recenzent vidí své recenze rozdělené do dvou skupin:
 *  přiřazené
 *  čekající na schválení
 * Přiřazené recenze vidí jako náhledy článků, které mu byly přiřazeny administrátorem k recenzování.
 * Recenze čekající na schválení může libovolně měnit a přepisovat
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

//je přihlášen? je recenzentem?
if (isset($user)) {
    if ($user["role"] == 2) {
        $i = 0;
        
        //přiřazené recenze
        echo '<h4>Přiřazené</h4>';
        if ($allPosts != null) {
            foreach ($allPosts as $post) {

                $isRew = 0;
                $allRecs = $params['db']->getRecs($post['id']);
                if ($allRecs != null) {
                    foreach ($allRecs as $rec) {
                        if ($rec['autor'] == $params['user']['login']) {
                            $isRew = 1;
                        }
                    }
                }

                if (($post['schvaleny'] == 0) &&
                        ( ($post['rec1'] == $params["user"]["login"]) || ($post['rec2'] == $params["user"]["login"]) || ($post['rec3'] == $params["user"]["login"]) ) &&
                        ($isRew == 0)
                ) {
                    $i++;

                    echo '<div class="container-fluid">';
                    echo '<div class="posts">';

                    echo '<div class="well well-sm well-top">';

                    //dosavadní hodnocení článku
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

                    echo "</div>";

                    echo "<div class='well well-bottom'>";

                    echo '<div class="floatright">' . $post['datum'] . '</div>';

                    echo $post['autor'] . "<br>";
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }
        }

        if ($i == 0) {
            echo 'Žádné přiřazené recenze';
        }
        
        //recenze čekající na schválení
        echo '<h4>Čekající na schválení</h4>';
        $allRecs = $params['db']->allRecs();
        if ($allRecs != null) {

            $recNum = 0;
            foreach ($allRecs as $rec) {
                $post = $params['db']->getPost($rec['prispevek']);
                if (($rec['autor'] == $user["login"]) && ($post['schvaleny'] == 0)) {
                    $recNum++;
                }
            }

            //výběr stránky
            if (($pg * 3) > ($recNum + 2)) {
                $pg = 1;
            }

            $recNum = 0;
            foreach ($allRecs as $rec) {
                $post = $params['db']->getPost($rec['prispevek']);

                if (($rec['autor'] == $user["login"]) && ($post['schvaleny'] == 0)) {
                    $recNum++;
                    if (($recNum >= ($pg - 1) * 4) && ($recNum < ($pg) * 4)) {
                        echo '<div class="container-fluid">';
                        echo '<div class="posts">';

                        echo '<div class="well well-sm well-top">';

                        //edit recenze
                        echo '<a class="floatright" href="/index.php?page=editPage&idr=' . $rec['id'] . '">
                        <span class="glyphicon glyphicon-pencil"></span>
                        </a>
                         ';

                        //celkové hodnocení zvolené recenzentem
                        echo '<div class="floatright">';
                        for ($i = 0; $i < $rec['celkove']; $i++) {
                            echo '<span class="glyphicon glyphicon-star"></span> ';
                        }
                        for ($l = 0; $l < (5 - $rec['celkove']); $l++) {
                            echo '<span class="glyphicon glyphicon-star-empty"></span>';
                        }
                        echo '</div>';

                        //je recenze aktuální nebo proběhl od jejího publikování update příspěvku
                        if ($rec['aktualni'] == 0) {
                            echo '<span class="label label-warning">Staré</span> <span class="glyphicon glyphicon-none"></span>';
                        }

                        //odkaz na článek
                        echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>Rec:"
                        . $post['nazev'] . "</span></a>";
                        echo '</div>';

                        echo "<div class='well well-bottom'>";

                        echo '<div class="floatright">' . $rec['datum'] . '</div>';

                        echo '<br>';
                        $obsahR = str_replace("\n", "<br>", $rec['obsah']);

                        echo $obsahR;
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
        }
        if ($recNum == 0) {
            echo 'Žádné recenze u neschválených příspěvků';
        }

        //šipky na orientaci mezi stránkami
        echo '<div class="col-md-offset-5">';
        if ($pg > 1) {
            echo '<a class="btn" href="/index.php?page=myRec&pg=' . ($pg - 1) . '">
                    <<
                    </a>
                     ';
        }

        if (($recNum / 3) > $pg) {
            echo '<a class="btn" href="/index.php?page=myRec&pg=' . ($pg + 1) . '">
                    >>
                    </a>
                     ';
        }
    } else {
        echo '<h2><span class="glyphicon glyphicon-remove"></span> Nedostatečné oprávnění</h2>';
    }
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';

