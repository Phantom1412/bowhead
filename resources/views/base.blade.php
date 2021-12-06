<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bowhead</title>

    <!-- dojichart will work for now, something simple was needed -->
    <link href="https://raw.githubusercontent.com/dojichart/dojichart/master/dist/dojichart.min.css" rel="stylesheet" type="text/css">
    <script src="https://raw.githubusercontent.com/dojichart/dojichart/master/dist/dojichart.min.js"></script>
    <link href="/css/camphor.scss" rel="stylesheet" type="text/css">
    <link href="/css/button.scss" rel="stylesheet" type="text/css">
    <link href="/css/lit.css" rel="stylesheet" type="text/css">
    <link href="{{ url(mix('css/app.css')) }}" rel="stylesheet" type="text/css" media="all">

    <!-- Styles -->
    <style>
        ::-webkit-scrollbar {
            -webkit-appearance: none;
            width: 7px;
        }
        ::-webkit-scrollbar-thumb {
            border-radius: 4px;
            background-color: rgba(0,0,0,.5);
            box-shadow: 0 0 1px rgba(255,255,255,.5);
        }
        body {
            font-family: Camphor, Open Sans, Segoe UI, sans-serif;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #8eb4cb;
        }
        .scrolly {
            border: 0px solid black;
            overflow-x: visible;
            overflow-y: scroll;
            height: 250px;
            max-heigth:250px;
        }
        .err {
            color: #ff6666;
            padding: 0 25px;
            letter-spacing: .1rem;
        }
        .c {
            background-color: #f5f5f5;
        }
        a { color: #FF0000; }
    </style>
</head>
<body>
    @yield('content')
    
    @yield('javascript')
</body>
</html>
