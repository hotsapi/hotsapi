<nav class="navbar navbar-inverse">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ url('/') }}"><img src="{{asset('logos/horizontal_light.png')}}"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li {{ setActive('/') }}>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li {{ setActive('upload') }}>
                    <a href="{{ url('upload') }}">Upload replays</a>
                </li>
                <li {{ setActive('docs') }}>
                    <a href="{{ url('docs') }}">API Documentation</a>
                </li>
                <li {{ setActive('faq') }}>
                    <a href="{{ url('faq') }}">FAQ</a>
                </li>
            </ul>
            <p class="navbar-text navbar-right">Replays uploaded: {{ $totalReplayCount }}</p>
        </div>
    </div>
</nav>
