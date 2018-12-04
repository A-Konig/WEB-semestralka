<?php

$allPosts = $params['db']->allPosts();
$allUsers = $params['db']->allUsers();

echo '<div class="container-fluid">';

//pro přihlášené uživatele
if ($params["user"] != null) {
    echo '<a href="index.php?page=newPost"><button type="button" class="btn floatright">Nový příspěvek</button></a>';
}

//pro všechny

if ($allPosts != null) {
    foreach ($allPosts as $index) {
        $index['autor'];
        $index['ID'];
        $index['obsah'];
        $index['tag'];
        
        $author;
        foreach ($allUsers as $user) {
            if ($user['ID'] == $index['autor']) {
                $author = $user;
                break;
            }
        }
        
        echo '<div class="container-fluid">';
            echo '<div class="posts">';
            echo "<div class='well well-sm well-top'>".$index['nazev']."</div>";   
            echo "<div class='well well-bottom'>";
            echo $author['login']."<br>#tagy";
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

echo '</div>';

