<?php

$allPosts = $params['db']->allPosts();
$allUsers = $params['db']->allUsers();

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

//pro přihlášené uživatele
echo '<span class="floatright">';
if ($user != null) {
    if ($user["role"] == 3) {
        echo '<a href="index.php?page=myPosts"><button type="button" class="btn">Mé příspěvky</button></a> ';
        if ($user['block'] == '0') {
            echo '<a href="index.php?page=newPost"><button type="button" class="btn">Nový příspěvek</button></a> ';
        }
    } else
    if ($user["role"] == 2) {
        echo '<a href="index.php?page=myRec"><button type="button" class="btn">Mé recenze</button></a>';
    } else
    if ($user["role"] == 1) {
        echo '<a href="index.php?page=toPublish"><button type="button" class="btn">Ke schválení</button></a>';
    }
}
echo '</span>';

//pro všechny
if ($allPosts != null) {
    
    foreach ($allPosts as $index) {
        if ($index['schvaleny'] == 1) {
            $index['autor'];
            $index['id'];
            $index['obsah'];

            echo '<div class="container-fluid">';
            echo '<div class="posts">';
    
            echo '<div class="well well-sm well-top">';

            //pro přihlášené uživatele
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

            echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $index['id'] . "'><span class='extendLink'>"
            . $index['nazev'] . "</span></a>";

            echo "</div>";


            echo "<div class='well well-bottom'>";

            echo '<div class="floatright">'.$rec['datum'].'</div>';
            
            echo '<div>';
            echo '</div>';

            echo $index['autor'] . "<br>" ;
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

  
    }
}

echo '</div>';

