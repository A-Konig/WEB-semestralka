<?php


if ($user != null) {
    echo '
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <ul class="nav navbar-nav">
                    <li class=""><a>';
    
    echo $user["login"].'</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/index.php?page=settings"><span class="glyphicon glyphicon-cog"></span> Nastavení</a></li>
                    <li>
                        <a>
                        <form class="form-inline" action="" method="POST">
                            <input type="hidden" name="log" value="logout">
                            <button type="submit" class="linkButton" name="submit"> <span class="glyphicon glyphicon-log-out"></span> Odhlásit</button>
                        </form>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    ';
    
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

}