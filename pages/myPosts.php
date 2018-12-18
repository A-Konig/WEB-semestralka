<?php

$allPosts = $params['db']->allPosts();

echo '<div class="container-fluid">';

if (isset($params["error"])) {
    echo '<div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Chyba!</strong> '.$params["error"].'
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

if (isset($params["user"])) {
    if ($params["user"]["role"] == 3) {
        
        echo '<h4>Zamítnuté</h4>';
        $i = 0;
        foreach ($allPosts as $post) {
            if (($post['autor'] == $params["user"]["login"]) && ($post['schvaleny'] == -1)) {
                $i++;
                echo '<div class="posts">';

                echo '<div class="well well-sm well-top">';

                //odmítnutí příspěvku
                echo '
                    <form class="form-inline floatright" action="" method="POST">
                        <input type="hidden" name="post" value="delete">
                        <input type="hidden" name="idPost" value="' . $post['id'] . '">
                        <button type="submit" class="linkButton" name="submit"> <span class="glyphicon glyphicon-trash"></span></button>
                    </form>
                     ';                
                
                //edit postu
                echo '<a class="floatright" href="/index.php?page=editPage&idp='.$post['id'].'">
                    <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                     ';
                
                
                echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>"
                . $post['nazev'] . "</span></a>";
                echo '</div>';

                echo "<div class='well well-bottom'>";

                echo $post['autor'] . "<br>";
                echo '</div>';
                
                echo '</div>';
            }
           
        }
        if ($i == 0) {
            echo 'Žádné zamítnuté příspěvky';
        }
        echo '<hr>';
        
        echo '<h4>Čekající na schválení</h4>';
        $i = 0;
        foreach ($allPosts as $post) {
           
            if (($post['autor'] == $params["user"]["login"]) && ($post['schvaleny'] == 0)) {
                $i++;
                echo '<div class="posts">';

                echo '<div class="well well-sm well-top">';

                //odmítnutí příspěvku
                echo '
                    <form class="form-inline floatright" action="" method="POST">
                        <input type="hidden" name="post" value="delete">
                        <input type="hidden" name="idPost" value="' . $post['id'] . '">
                        <button type="submit" class="linkButton" name="submit"> <span class="glyphicon glyphicon-trash"></span></button>
                    </form>
                     ';                
                
                //edit postu
                echo '<a class="floatright" href="/index.php?page=editPage&idp='.$post['id'].'">
                    <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                     ';
                
                
                echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>"
                . $post['nazev'] . "</span></a>";
                echo '</div>';

                echo "<div class='well well-bottom'>";

                echo $post['autor'] . "<br>";
                echo '</div>';
                
                echo '</div>';
            }
        }
        if ($i == 0) {
            echo 'Žádné příspěvky čekající na schválení';
        }
        echo '<hr>';
        
        echo '<h4>Publikované</h4>';
        $i = 0;
        foreach ($allPosts as $post) {
            if (($post['autor'] == $params["user"]["login"]) && ($post['schvaleny'] == 1)) {
                $i++;
                echo '<div class="posts">';

                echo '<div class="well well-sm well-top">';
                echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>"
                . $post['nazev'] . "</span></a>";
                echo '</div>';

                echo "<div class='well well-bottom'>";

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

                echo $post['autor'] . "<br>";
                echo '</div>';
                echo '</div>';
            }
        }
         if ($i == 0) {
            echo 'Žádné publikované příspěvky';
        }
        echo '<hr>';
    }
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}


echo '</div>';

