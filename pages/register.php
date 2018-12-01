<?php

echo '<div class="page">';

//pro přihlášené uživatele
if ($user != null) {
    
    echo 'Jste přihlášen jako uživatel ' . $params["user"]["login"] 
    . '<br><br>';
    echo 'Nejste to vy?'
    . '<br><br>';
    echo '<form action="" method="POST">';
    echo '<input type="hidden" name="log" value="logout">';
    echo '<input class="inputButton" type="submit" name="submit" value="Odhlásit">';
    echo '</form>';

//pro nepřihlášené uživatele    
} else {
    
    echo '<h2>Registrace:</h2>';
    echo '<form class = "submit" action="" method="POST" >';
    
    echo '<span class="formHead">Login<span style="color:red">*</span>:</span> <input type="text" name="login" required><br>';
    echo '<span class="formHead">Jméno:</span> <input type="text" name="name"><br>';
    echo '<span class="formHead">E-mail<span style="color:red">*</span>:</span> <input type="email" name="email" required><br>';
    echo '<span class="formHead">Heslo<span style="color:red">*</span>:</span> <input type="password" name="password" required><br>';
    
    echo '<input type="checkbox" name="agree" required> <span style="color:red">*</span> I have read and understood <a href="index.php?page=terms">The terms and conditions</a><br>';

    echo '<br>';
    echo '<input type="hidden" name="log" value="register">';
    echo '<input class="inputButton" type="submit" name="submit" value="Registrovat">';
    echo '</form>';
    
    echo '<span style="color:red">*</span><span style="font-size:10px"> povinná položka</span><br>';
}

echo '</div>';
