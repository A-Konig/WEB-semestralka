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


if (isset($user)) {
    if ($user["role"] == 2) {
        $i = 0;
        echo '<h4>Přiřazené</h4>';
        foreach ($allPosts as $post) {
            
            $isRew = 0;
            $allRecs = $params['db']->getRecs($post['id']);
            if ($allRecs != null) { 
                foreach ($allRecs as $rec) {
                    if ($rec['autor']==$params['user']['login']) {
                        $isRew = 1;
                    }
                }
            }            
            
            if ( ($post['schvaleny'] == 0) &&
                 ( ($post['rec1'] == $params["user"]["login"]) || ($post['rec2'] == $params["user"]["login"]) || ($post['rec3'] == $params["user"]["login"]) ) &&
                 ($isRew == 0)   
               ) {
                $i++;
                
                echo '<div class="container-fluid">';
                echo '<div class="posts">';
    
                echo '<div class="well well-sm well-top">';

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
                
                echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>"
                        . $post['nazev'] . "</span></a>";

                echo "</div>";


                echo "<div class='well well-bottom'>";

                echo '<div class="floatright">'.$post['datum'].'</div>';

                echo $post['autor'] . "<br>";
                echo '</div>';
                echo '</div>';
                echo '</div>';
                
                
            } 
        }
        
        if ($i == 0) {
            echo 'Žádné přiřazené recenze';
        }
        
        echo '<h4>Čekající na schválení</h4>';
        $i = 0;
        
        $allRecs = $params['db']->allRecs();
        if ($allRecs != null) {
            foreach ($allRecs as $rec) {
                $post = $params['db']->getPost($rec['prispevek']);
            
                if (($rec['autor'] == $params["user"]["login"]) && ($post['schvaleny'] == 0)) {
                    $i++;
                    echo '<div class="container-fluid">';
                    echo '<div class="posts">';

                    echo '<div class="well well-sm well-top">';

                    //edit recenze
                    echo '<a class="floatright" href="/index.php?page=editPage&idr='.$rec['id'].'">
                        <span class="glyphicon glyphicon-pencil"></span>
                        </a>
                         ';
                
                    //hodnocení
                    echo '<div class="floatright">';
                    for ($i = 0; $i < $rec['celkove']; $i++) {
                        echo '<span class="glyphicon glyphicon-star"></span> ';
                    }
                    for ($l = 0; $l < (5 - $rec['celkove']); $l++) {
                        echo '<span class="glyphicon glyphicon-star-empty"></span>';
                }
                    echo '</div>';
                
                    echo "<a class='undecoratedLink' href='/index.php?page=viewPost&id=" . $post['id'] . "'><span class='extendLink'>Rec:"
                     . $post['nazev'] . "</span></a>";
                    echo '</div>';

                    echo "<div class='well well-bottom'>";

                    echo '<div class="floatright">'.$rec['datum'].'</div>';
                    
                    echo '<br>';
                    echo $rec['obsah'];
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            
            }
        }
        if ($i == 0) {
            echo 'Žádné recenze u neschválených příspěvků';
        }
    }
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';

