<?php

echo '<div class="page">';

//pro přihlášené uživatele
if ($user != null) {
    if ($user['roleData']['ID'] == '2') {
        echo '<div class="itemName">';
        echo '<h2>Nový příspěvek:</h2>';
        echo '</div><div class="item">';

        echo '<form class = "submit" action="" method="POST">';

        echo '<span class="formHead">Název:</span> <input class="postLine" type="text" name="Název" required><br>';
        echo '<span class="formHead">Obsah:</span> <input class="postContent" type="text" name="Obsah" required><br>';
        echo '<span class="formHead">Tagy:</span> <input class="postLine" type="text" name="Tags"><br>';

        echo '<br>';
        echo '<input type="hidden" name="postAction" value="newPost"><br>';
        echo '<span class="floatcenter"><input class="inputButton" type="submit" name="submit" value="Nový příspěvek"></span>';
        echo '</form>';
        echo '</div>';
    } else {
        echo 'Tato stránka je jen pro přispěvatele.';
    }
//pro nepřihlášené uživatele    
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a class="button" href="index.php?page=login">Login</a>';
}

echo '</div>';
