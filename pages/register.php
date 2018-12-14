<?php

echo '<div class="container-fluid">';

if (isset($params["error"])) {
    echo '<div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Chyba!</strong> '.$params["error"].'
          </div>';
    unset($params["error"]);
} else if (isset($params["message"])) {
    echo '<div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Úspěch!</strong> '.$params["message"].'
          </div>';
    unset($params["message"]);
}

//pro přihlášené uživatele
if ($params["user"] != null) {
    
    echo 'Jste přihlášen jako uživatel ' . $params["user"]["login"] . '<br>';
    echo 'Nejste to vy?';
    echo '<form action="" method="POST">';
    echo '<input type="hidden" name="log" value="logout">';
    echo '<input class="btn" type="submit" name="submit" value="Odhlásit">';
    echo '</form>';

//pro nepřihlášené uživatele    
} else {
    
    echo '<h2>Registrace:</h2>';
    
    echo '<form class="form-horizontal" action="" method="POST">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Login*:</label>
                    <div class="col-sm-3">
                       <input type="text" class="form-control" name="login" placeholder="Uživatelské jméno" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Jméno:</label>
                    <div class="col-sm-3">
                       <input type="text" class="form-control" name="name" placeholder="Jméno">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Email*:</label>
                    <div class="col-sm-3">
                       <input type="email" class="form-control" name="email" placeholder="E-mail" required>
                    </div>
                </div>                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="password">Heslo*:</label>
                    <div class="col-sm-3"> 
                        <input type="password" class="form-control" name="password" placeholder="Heslo" required>
                    </div>
                </div>
                    <div class="form-group"> 
                        <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label><input type="checkbox">* I have read and understood <a href="index.php?page=terms">The terms and conditions</a></label>
                        </div>
                    </div>
                </div>                
                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-7">
                        <input type="hidden" name="log" value="register">
                        <input class="btn" type="submit" name="submit" value="Registrovat">
                    </div>
                </div>
         </form>';  
    echo '<span style="color:red">*</span><span> povinná položka</span><br>';

/*    
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
*/
}

echo '</div>';
