<?php

echo '<h2>Přihlášení uživatele:</h2>';
echo '<form action="" method="POST">';
    echo 'Login: <input type="text" name="Login">';
    echo '<input type="hidden" name="action" value="login"><br>';
    echo 'Heslo: <input type="password" name="Heslo">';
    echo '<input type="hidden" name="action" value="password"><br>';
    echo '<br>';
    echo '<input type="submit" name="potvrzeni" value="Přihlásit">';
echo '</form>';
echo '<br>';
