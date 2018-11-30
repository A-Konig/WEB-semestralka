<?php

/* asi bysme tady měli šahat do databáze a vytahovat vodtamtuď příspěvky */

echo '<div class="page">';

//pro přihlášené uživatele
if ($params["user"] != null) {
    echo '<a href="index.php?page=newPost" class="button floatright">Nový příspěvek</a>';
}

//pro všechny
echo '<div class="posts">';
echo "<div class='itemName'>Název</div>";
echo "<div class='item'>";
echo "Autor <br>#tagy";
echo '</div>';
echo '</div>';

echo '</div>';
echo '<br>';

