<?php

echo '<div class="container-fluid">';

if (isset($params["error"])) {
    echo '<div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Chyba!</strong> ' . $params["error"] . '
          </div>';
    unset($params["error"]);
} else if (isset($params["message"])) {
    echo '<div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Úspěch!</strong> ' . $params["message"] . '
          </div>';
    unset($params["message"]);
}


if (isset($params['user'])) {

    //upravení recenze
    if (isset($_GET['idr'])) {
        $rec = $params['db']->getOneRec($_GET["idr"]);
        if ($rec != null) {
            if ($rec['autor'] == $params['user']['login']) {

                echo '<h4>Upravit recenzi:</h4>';
                
                  echo '<form class="form-horizontal" action="" method="POST"">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="content">Obsah:</label>
                            <div class="col-sm-10"> 
                                <textarea class="form-control" rows="10" name="content" placeholder="Text recenze" required>'.$rec["obsah"].'</textarea>
                            </div>
                        </div>';

            //hodnocení originality      ¨
                  echo  '<div class="form-group">
                            <label class="control-label col-sm-2" for="orig">Originalita</label>
                        ';
                  for ($i = 1; $i<6; $i++) {
                      if ($rec["originalita"] == $i) {
                            echo '<label class="radio-inline"><input type="radio" name="orig" value="'.$i.'" checked>';
                      } else {
                            echo '<label class="radio-inline"><input type="radio" name="orig" value="'.$i.'">';
                      }
                      for ($j = 0; $j < $i; $j++) {
                            echo '<span class="glyphicon glyphicon-star-empty"></span>';
                      }
                      echo '</label>';
                  }
                  echo '</div>';
                  
            //hodnocení jazyka      
                  echo  '<div class="form-group">
                            <label class="control-label col-sm-2" for="lang">Jazyk</label>
                        ';
                  for ($i = 1; $i<6; $i++) {
                      if ($rec["jazyk"] == $i) {
                            echo '<label class="radio-inline"><input type="radio" name="lang" value="'.$i.'" checked>';
                      } else {
                            echo '<label class="radio-inline"><input type="radio" name="lang" value="'.$i.'">';
                      }
                            for ($j = 0; $j < $i; $j++) {
                                echo '<span class="glyphicon glyphicon-star-empty"></span>';
                            }
                      echo '</label>';
                  }
                  echo '</div>';

            //celkové hodnocení      
                  echo  '<div class="form-group">
                            <label class="control-label col-sm-2" for="summary">Celkově</label>
                        ';
                  for ($i = 1; $i<6; $i++) {
                      if ($rec["celkove"] == $i) {
                            echo '<label class="radio-inline"><input type="radio" name="summary" value="'.$i.'" checked>';
                      } else {
                            echo '<label class="radio-inline"><input type="radio" name="summary" value="'.$i.'">'; 
                      }
                            for ($j = 0; $j < $i; $j++) {
                                echo '<span class="glyphicon glyphicon-star-empty"></span>';
                            }
                      echo '</label>';
                  }
                  echo '</div>';
                  
                  echo '     </div>
                           <div class="form-group"> 
                        <div class="col-sm-offset-2 col-sm-9">
                            <input type="hidden" name="idRec" value="' . $_GET['idr'] . '">
                            <input type="hidden" name="rec" value="edit">
                            <input class="btn" type="submit" name="submit" value="Publikovat">
                        </div>
                    </div>
                </form>';  
            }
        } else {
            echo '<h2><span class="glyphicon glyphicon-remove"></span> Nedostatečné oprávnění</h2>';
        }

        //upravení příspěvku    
    } else if (isset($_GET['idp'])) {
        $post = $params['db']->getPost($_GET["idp"]);
        if ($post != null) {
            if ( ($post['autor'] == $params['user']['login']) && ($post['schvaleny'] == 0) ) {
                echo '<h4>Upravit příspěvek:</h4>';
                
                echo '<form class="form-horizontal" action="" method="POST"">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="headline">Nadpis:</label>
                    <div class="col-sm-10">
                       <input type="text" class="form-control" name="headline" value="'.$post['nazev'].'" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="content">Post:</label>
                    <div class="col-sm-10"> 
                        <textarea class="form-control" rows="20" name="content" placeholder="Text článku" required>'.$post['obsah'].'</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="file">Příloha:</label>
                    <div class="col-sm-10">
                           <input type="file" class="form-control-static" name="file" id="file">
                    </div>
                </div>      
                    
                    <div class="form-group"> 
                        <div class="col-sm-offset-2 col-sm-9">
                            <input type="hidden" name="idPost" value="' . $_GET['idp'] . '">
                            <input type="hidden" name="post" value="edit">
                            <input class="btn" type="submit" name="submit" value="Uložit">
                        </div>
                    </div>
                    
                </form>';
                
            }
        } else {
            echo '<h2><span class="glyphicon glyphicon-remove"></span> Nedostatečné oprávnění</h2>';
        }
    }
}


echo '</div>';


//asi formulář předvyplněnej a na submit se dělá update
