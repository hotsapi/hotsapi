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
        .partner-link-wrapper{
          display: flex;
          flex-wrap: wrap;
          justify-content: center;
        }
        .partner-link {
            line-height: 75px;
            margin: .5em;
            width: 42%;
            
        }
        .partner-link img{
          max-height: 64px;
          max-width: 100%;
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
            <p>You can see news on our <a href="https://www.patreon.com/hotsapi">Patreon page</a></p>

            <br><br>
            <h1>Services that use our API</h1>
            <div class="partner-link-wrapper">
                <a target="_blank" href="http://stormspy.net" class="partner-link"><img src="{{asset('/img/logo-stormspy.png')}}"></a>
                <a target="_blank" href="http://hots.guide" class="partner-link"><img src="{{asset('/img/logo-hotsguide.png')}}"></a>
                <a target="_blank" href="http://heroes.report" class="partner-link"><img src="{{asset('/img/logo-heroesreport.png')}}"></a>
                <a target="_blank" href="https://hots.dog" class="partner-link"><img src="{{asset('/img/logo-hotsdog.svg')}}"></a>
                <a target="_blank" href="https://play.google.com/store/apps/details?id=com.heroescompanion.app" class="partner-link"><img src="{{asset('/img/logo-heroescompanion.png')}}"></a>
    			      <a target="_blank" href="https://hots.academy" class="partner-link"><img src="{{asset('/img/logo-hotsacademy.png')}}"></a>
    			      <a target="_blank" href="https://heroesshare.net" class="partner-link"><img src="{{asset('/img/logo-heroesshare.png')}}"></a>
          	    <a target="_blank" href="https://heroesprofile.com" class="partner-link"><img src="{{asset('/img/logo-heroesprofile.png')}}"></a>
            </div>
        </div>
    </div>

@endsection
