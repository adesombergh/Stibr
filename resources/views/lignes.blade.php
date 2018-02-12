<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }
            #lines {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            #lines li {
                margin: 7px;
            }    
            #lines li a{
                text-decoration: none;
            }

            #lines .numero{
                width: 35px;
                height: 35px;
                display: inline-block;
                border-radius: 4px;
                margin-right: 10px;
                text-align: center;
                vertical-align: middle;
                line-height: 35px;
            }
        </style>
    </head>
    <body>
        <ul id="lines">
            @foreach ($lignes as $ligne)
            @foreach ($ligne->route_directions as $slug => $direction)
            <li>
                <a href="{{ route('ligne',['id'=>$ligne->route_short_name,'direction'=>$slug ]) }}">
                    <span class="numero" style="background-color: {{ $ligne->route_color }}; color: {{ $ligne->route_text_color }}">
                        {{ $ligne->route_short_name }} 
                    </span>
                    <span class="titre">
                        {{ $direction }} 
                    </span>
                </a>
            </li>
            @endforeach
            @endforeach
        </ul>
    </body>
</html>
