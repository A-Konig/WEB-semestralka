<?php

$posts = $params['db']->allPosts();
$recs = $params['db']->allRecs();
$allUsers = $params['db']->allUsers();

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

if ($params["user"] != null) {
    if ($params["user"]["role"] == 1) {

        echo '<h3>Příspěvky</h3>';
        if ($posts == null) {
            echo '<p>Žádné příspěvky čekající na schválení</p>';
        } else {
            $i = 0;
            foreach ($posts as $post) {
                if ($post['schvaleny'] == 0) {
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
                    if (isset($recsPost)) {
                        $num = count($recsPost);
                    }
                    
                    echo '<div class="floatright">';
                    echo $num.'/3';
                    echo '</div>';
                    
                    if ($num >= 3) {
                        echo '
                            <form class="form-inline floatright" action="" method="POST">
                                <input type="hidden" name="post" value="publish">
                                <input type="hidden" name="idPost" value="' . $post['id'] . '">
                                <button type="submit" class="linkButton" name="submit"> <span class="glyphicon glyphicon-ok"></span></button>
                            </form>
                             ';
                    }

                    echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>"
                    . $post['nazev'] . "</span></a>";

                    echo "</div>";

                    echo "<div class='well well-bottom well-padd'>";

                    echo '<div class="floatright">';

                    echo '<div>';
                    //nastavování recenzentů
                    for ($i = 1; $i < 4; $i++ ) {
                        echo '
                                <form class="form-inline floatright" action="" method="POST">Recenzent:<div class="form-group">
                                <select  id="rec" name="loginRec">';
                        echo '<option></option>';
                        foreach ($allUsers as $user) {
                            $ok = 1;
                            foreach ($recsPost as $postRec) {
                                if ($postRec['autor'] == $user['login']) {
                                    $ok = 0;
                                }
                            }
                            
                            if ($user['role'] == 2 && $user['block'] == 0 && $ok == 1 ) {
                                if ($user[login] == $post['rec'.$i]) {
                                    echo '<option selected>' . $user['login'] . '</option>';
                                } else {
                                    echo '<option>' . $user['login'] . '</option>';
                                }
                            }
                            
                        }
                        echo '</select>
                            </div>

                            <input type="hidden" name="post" value="setRec">
                            <input type="hidden" name="numberRec" value="'.$i.'">
                            <input type="hidden" name="idPost" value="' . $post['id'] . '">
                            <button type="submit" class="" name="submit">Set</span></button>
                        </form>
                        </br>    
                         ';
                        
                        
                    }
                    echo '</div>';

                    echo '</div>';

                    echo $post['autor'] . "<br>";
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';

                    $i++;
                }
            }
            if ($i == 0) {
                echo '<p>Žádné příspěvky čekající na schválení</p>';
            }
            
        }
        
    } else {
        echo 'Nedostatečné oprávnění';
    }
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';
