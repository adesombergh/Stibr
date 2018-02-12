<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
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
                text-decoration: none;
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
            ul {
            list-style: none;
            padding: 0;
            margin: 0;
            }

            li {
                padding-left: 1em; 
                text-indent: -.7em;
            }

            li::before {
                content: "o ";
                color: grey;
                font-weight: 800;
            }
            li.red::before {
                content: "x ";
                color: red;
            }
        </style>
    </head>
    <body>
        <h1>{{ $route->route_short_name }} - {{ $route->route_long_name }}</h1>
        <ul>
        @foreach($stops as $stop)
             @if(isset($stop->here))
            <li class="red">
            @else
            <li>
            @endif
            {{ $stop->stop_name }}
            </li>
        @endforeach
        </ul>
    </body>
</html>
