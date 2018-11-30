<?php

echo '<h2>Registrace:</h2>';
echo '<form action="" method="POST">';
    echo 'Login<span style="color:darkred">*</span>: <input type="text" name="Login">';
    echo '<input type="hidden" name="action" value="login"><br>';
    echo 'Jméno: <input type="text" name="Name">';
    echo '<input type="hidden" name="action" value="name"><br>';
    echo 'E-mail<span style="color:darkred">*</span>: <input type="text" name="Email">';
    echo '<input type="hidden" name="action" value="email"><br>';
    echo 'Heslo<span style="color:darkred">*</span>: <input type="password" name="Heslo">';
    echo '<input type="hidden" name="action" value="password"><br>';
    echo '<input type="checkbox" name="agree" value="Agree"> <span style="color:darkred">*</span> I have read and understood <a href="index.php?page=terms">The terms and conditions</a><br>';
    echo '<br>';
    echo '<input type="submit" name="potvrzeni" value="Registrovat"><br>';
echo '</form>';
echo '<span style="color:darkred">*</span><span style="font-size:10px"> povinná položka</span><br>';
echo '<br>';