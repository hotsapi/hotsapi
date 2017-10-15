@extends('template')
@section('title', 'HotsApi')

@section('head')
    <style>
        .main-text {
            padding: 3rem 1.5rem;
            text-align: center;
        }
        .main-text > div {
            text-align: center;
            max-width: 600px;
            margin:auto;
        }
        .partner-link {
            line-height: 75px;
        }
    </style>
@endsection

@section('content')

    <div class="main-text">
        <div align="center">
            <h1>Welcome to HotsApi</h1>
            <h4>HotsApi is an open Heroes of the Storm replay database where everyone can download replays</h4>
            <p>
                We want all developers to have access to a large community replay database. Whether you want to continuously get
                new replays, download them all once, or just query their metadata, our service helps
                you to start development faster.
            </p>
            <p>Check out our <a href="https://www.reddit.com/r/heroesofthestorm/comments/6wqlvd/introducing_hotsapinet_an_open_source_shared/">Reddit thread</a></p>

            <br><br>
            <h1>Services that use our API</h1>
            <a href="http://stormspy.net" class="partner-link"><img src="https://stormspy.net/img/stormspy-logo.png"></a>
            <a href="http://hots.guide" class="partner-link"><img src="{{asset('/img/logo-hotsguide.png')}}"></a>
            <a href="http://heroes.report" class="partner-link"><img src="{{asset('/img/logo-heroesreport.png')}}"></a>
        </div>
    </div>

@endsection
