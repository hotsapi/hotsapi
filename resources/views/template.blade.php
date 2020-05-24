<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    @yield('head')
</head>
<body>
    @include('parts.nav')

    <div class="container">
        @yield('content')
    </div>

    <footer class="footer">
        <div align="center">
            Support us on <a href="https://www.patreon.com/bePatron?u=7326354">Patreon</a>
        </div>
        

    </footer>

    <script src="{{ mix('/js/app.js') }}"></script>
    @yield('body')
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-105491473-1', 'auto');
        ga('send', 'pageview');
    </script>
</body>
</html>
