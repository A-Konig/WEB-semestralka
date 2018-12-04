<?php

echo '<div class="page">';

if (isset($params["error"])) {
    echo '<div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Chyba!</strong> '.$params["error"].'
          </div>';
    unset($params["error"]);
}

//pro přihlášené uživatele
if ($user != null) {
    if ($user['roleData']['ID'] != '3') {
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
        
/*        
        echo '<form class = "submit" action="" method="POST">';

        echo '<span class="formHead">Název:</span> <input class="postLine" type="text" name="Název" required><br>';
        echo '<span class="formHead">Obsah:</span> <input class="postContent" type="text" name="Obsah" required><br>';
        echo '<span class="formHead">Tagy:</span> <input class="postLine" type="text" name="Tags"><br>';

        echo '<br>';
        echo '<input type="hidden" name="postAction" value="newPost"><br>';
        echo '<span class="floatcenter"><input class="inputButton" type="submit" name="submit" value="Nový příspěvek"></span>';
        echo '</form>';
    } else {
        echo 'Tato stránka je jen pro přispěvatele.';
*/
    }

//pro nepřihlášené uživatele    
} else {
    echo 'Tato stránka je pouze pro přihlášené uživatele<br>';
    echo '<a href="index.php?page=login"><button type="button" class="btn">Login</button></a>';
}

echo '</div>';