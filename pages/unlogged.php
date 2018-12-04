<?php

//echo '<div class="user">';

if ($params["user"] != null) {
    echo '
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <ul class="nav navbar-nav">
                    <li class=""><a href="#">'.$params["user"]["login"].'</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/index.php?page=register"><span class="glyphicon glyphicon-cog"></span> Nastavení</a></li>
                    <li>
                        <a>
                        <form class="form-inline" action="" method="POST">
                            <span class="glyphicon glyphicon-log-out"></span>
                            <input type="hidden" name="log" value="logout">
                            <input type="submit" name="submit" class="linkButton" value="Odhlásit">
                        </form>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    ';


    

//    echo $params["user"]["login"];
//    echo '<form action="" method="POST">';
//    echo '<input type="hidden" name="log" value="logout">';
//    echo '<input class="inputButton floatright" type="submit" name="submit" value="Odhlásit">';
//    echo '</form>';
    
} else {
    echo '
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <ul class="nav navbar-nav">
                    <li class=""><a href="#">Nepřihlášen</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/index.php?page=register"><span class="glyphicon glyphicon-user"></span> Registrace</a></li>
                    <li><a href="/index.php?page=login"><span class="glyphicon glyphicon-log-in"></span> Přihlásit se</a></li>
                </ul>
            </div>
        </nav>
    ';

    
//    echo ' <span class="accountOp"><a class="button floatright" href="index.php?page=register">Register</a></span>';
//    echo ' <span class="accountOp"><a class="button floatright" href="index.php?page=login">Login</a></span> ';
}

//echo '</div>';