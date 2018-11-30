<?php

echo '<div class="user">';

if ($params["user"] != null) {
    echo $params["user"]["name"];
    echo '<form action="" method="POST">';
    echo '<input type="hidden" name="log" value="logout">';
    echo '<input class="inputButton floatright" type="submit" name="submit" value="Odhlásit">';
    echo '</form>';
    
} else {
    echo ' <span class="accountOp"><a class="button floatright" href="index.php?page=register">Register</a></span>';
    echo ' <span class="accountOp"><a class="button floatright" href="index.php?page=login">Login</a></span> ';
}

echo '</div>';