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
                <a target="_blank" href="https://heroes.report" class="partner-link"><img src="{{asset('/img/logo-heroesreport.png')}}"></a>
                <a target="_blank" href="https://heroesshare.net" class="partner-link"><img src="{{asset('/img/logo-heroesshare.png')}}"></a>
          	    <a target="_blank" href="https://www.heroesprofile.com" class="partner-link"><img src="{{asset('/img/logo-heroesprofile.png')}}"></a>
          	    <a target="_blank" href="https://play.google.com/store/apps/details?id=com.production.holender.hotsrealtimeadvisor" class="partner-link"><img src="{{asset('/img/logo-hotscomplete-google.png')}}"></a>
                <a target="_blank" href="https://itunes.apple.com/us/app/hots-complete/id1265870687" class="partner-link"><img src="{{asset('/img/logo-hotscomplete-apple.png')}}"></a>
            </div>
        </div>
    </div>

@endsection
