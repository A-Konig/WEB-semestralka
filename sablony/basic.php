<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Konference</title>


        <link rel="stylesheet" href="vendor/twbs/bootstrap/docs/dist/css/bootstrap.min.css">

        <style>
            {{ source('color.css') }}
        </style>

        <script src="vendor/components/jquery/jquery.min.js"></script>
        <script src="vendor/twbs/bootstrap/docs/dist/js/bootstrap.min.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </head>
    <body>
        <header>
            <h1>Konference</h1>
            {{ user | raw }}

        </header>

        <article>
            <div class='container-fluid'>
                <div class="row">
                    <div class=" col-sm-3 menu">
                        {% for page_key, page_title in pages %}
                        <a href="index.php?page={{ page_key }}"><div>{{ page_title }}</div></a>
                        {% endfor %}
                    </div>
                    <div class="col-sm-9">
                        <p> {{ contents | raw }}
                    </div>
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

