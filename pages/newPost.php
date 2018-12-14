<?php

echo '<div class="page">';

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
if ($user != null) {
    if ($user['roleData']['id'] == '3') {
        echo '<h2>Nový příspěvek:</h2>';

        echo '<form class="form-horizontal" action="" method="POST"">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="headline">Nadpis:</label>
                    <div class="col-sm-10">
                       <input type="text" class="form-control" name="headline" placeholder="Nadpis" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="content">Post:</label>
                    <div class="col-sm-10"> 
                        <textarea class="form-control" rows="20" name="content" placeholder="Text článku" required></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="file">Příloha:</label>
                    <div class="col-sm-10">
                           <input type="file" class="form-control-static" name="file" id="file">
                    </div>
                </div>      

                <div class="form-group">
                    <label class="control-label col-sm-2" for="tags">Tags:</label>
                    <div class="col-sm-10">
                       <input type="text" class="form-control" name="tags" placeholder="#tags">
                    </div>
                </div> 
                
                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-9">
                        <input type="hidden" name="post" value="newPost">
                        <input class="btn" type="submit" name="submit" value="Publikovat">
                    </div>
                </div>
         </form>';  
        
    }

//pro nepřihlášené uživatele    
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';