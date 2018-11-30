<?= php

?>

<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Konference</title>
        
        <style>
            {{ source('color.css') }}
            {{ source('responsive.css') }}
        </style>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </head>
    <body>
        <header>
            <h1>Konference</h1>
        </header>

        <article>
            <div class="col-12 user">
                {{ user | raw }}
            </div>
            <div class="row">
                <div class=" col-3 menu">
                    {% for page_key, page_title in pages %}
                    <a href="index.php?page={{ page_key }}"><div>{{ page_title }}</div></a>
                    {% endfor %}
                </div>
                <div class="col-9">
                    <p> {{ contents | raw }}
                </div>
            </div>
            <!--
            <div class="row foot">
                <div class="col-2">
                    
                </div>
                <div class="col-4">
                    
                </div>
                <div class="col-4">
                    
                </div>
                <div class="col-2">
                    
                </div>                
            </div>
            -->
        </article>
        <footer>
            Alex Konig<br>
            (c) 2018
        </footer>        
    </body>
</html>

