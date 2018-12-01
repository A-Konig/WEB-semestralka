<?php

$allPosts = $params['db']->allPosts();

echo '<div class="page">';

//pro přihlášené uživatele
if ($params["user"] != null) {
    echo '<a href="index.php?page=newPost" class="button floatright">Nový příspěvek</a>';
}

//pro všechny
echo '<div class="posts">';
if ($allPosts != null) {
    foreach ($allPosts as $index) {
        echo '<div class="listingBox">';
            echo '<div class="posts">';
            echo "<div class='itemName'>Název</div>";   
            echo "<div class='item'>";
            echo "Autor <br>#tagy";
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
echo '</div>';

echo '</div>';

