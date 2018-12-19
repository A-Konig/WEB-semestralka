<?php

/**
 * Stránka, která zobrazuje přehled všech publikovaných příspěvků.
 * Pokud je uživatel přihlášen a zároveň admin, má možnost příspěvky i mazat.
 */

$allPosts = $params['db']->allPosts();
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
echo '<span class="floatright">';
if ($user != null) {
    //odkaz na nový příspěvek (newPost) a stránku s autorovými příspěvky pro uživatele s oprávněním autor (myPosts)
    if ($user["role"] == 3) {
        echo '<a href="index.php?page=myPosts"><button type="button" class="btn">Mé příspěvky</button></a> ';
        echo '<a href="index.php?page=newPost"><button type="button" class="btn">Nový příspěvek</button></a> ';
    } else
    //pro recenzenty odkaz na jim přiřazené články k recenzi (myRecs)
    if ($user["role"] == 2) {
        echo '<a href="index.php?page=myRec"><button type="button" class="btn">Mé recenze</button></a>';
    } else
    //pro admina odkaz na stránku s příspěvky ke schválení (toPublish)
    if ($user["role"] == 1) {
        echo '<a href="index.php?page=toPublish"><button type="button" class="btn">Ke schválení</button></a>';
    }
}
echo '</span>';

//stránkování
$pg = 1;
if (isset($_GET['pg']) && filter_var($_GET['pg'], FILTER_VALIDATE_INT)) {
    $pg = $_GET['pg'];
}

//pro všechny
if ($allPosts != null) {

    $i = 0;
    foreach ($allPosts as $index) {
        if ($index['schvaleny'] == 1) {
            $i++;
        }
    }

    //výběr stránky
    if (($pg * 5) > ($i + 4)) {
        $pg = 1;
    }

    $i = 0;
    foreach ($allPosts as $index) {

        if ($index['schvaleny'] == 1) {
            $i++;
            if (($i >= ($pg - 1) * 6) && ($i < ($pg) * 6)) {
                $index['autor'];
                $index['id'];
                $index['obsah'];

                echo '<div class="container-fluid">';
                echo '<div class="posts">';

                echo '<div class="well well-sm well-top">';

                if ($params["user"] != null) {

                    //pro admina - mazání příspěvků
                    if ($params["user"]["role"] == 1) {
                        echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="post" value="delete">
                                <input type="hidden" name="idPost" value="' . $index['id'] . '">
                                <button type="submit" class="linkButton" name="submit"><span class="glyphicon glyphicon-trash"></span></button>
                            </form>
                          ';

                        echo '<span class="glyphicon glyphicon-none floatright"></span>';
                    }
                }

                //hodnocení
                echo '<div class="floatright">';
                echo 'Hodnocení: ';
                $value = 0;
                $outOf = 0;
                $recs = $params['db']->getRecs($index['id']);
                if ($recs != null) {
                    foreach ($recs as $rec) {
                        $outOf += 5;
                        $value += $rec['celkove'];
                    }
                }
                echo $value . ' / ' . $outOf;
                echo '</div>';

                //odkaz na stránku se zobrazením článku
                echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $index['id'] . "'><span class='extendLink'>"
                . $index['nazev'] . "</span></a>";

                echo "</div>";


                echo "<div class='well well-bottom'>";

                //datum zveřejnění
                echo '<div class="floatright">' . $index['datum'] . '</div>';

                echo '<div>';
                echo '</div>';

                //autor
                echo $index['autor'] . "<br>";
                
                //pokud k článku byl přiložen soubor zobrazí se na něj odkaz
                if ($index['file'] != null) {
                    $file = 'files/' . $index['file'];

                    if (file_exists($file)) {
                        echo '<a href="'. $file . '"><span class="glyphicon glyphicon-file"></span>  Zobrazit přiložený soubor</a>';
                    }
                }
                
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
    }

    if ($i == 0) {
        echo '<p>Žádné publikované příspěvky';
    }
    
    //šipky pro orientaci mezi stránkami
    echo '<div class="col-md-offset-5">';
    if ($pg > 1) {
        echo '<a class="btn" href="/index.php?page=posts&pg=' . ($pg - 1) . '">
                    <<
                    </a>
                     ';
    }

    if ($i > $pg * 5) {
        echo '<a class="btn" href="/index.php?page=posts&pg=' . ($pg + 1) . '">
                    >>
                    </a>
                     ';
    }
    echo '</div>';
}

echo '</div>';

