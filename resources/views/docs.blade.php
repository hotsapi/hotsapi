@extends('template')
@section('title', 'HotsApi')

@section('head')
    <style>
        .table {
            width: auto;
        }
        tr{
            border-top: hidden;
        }
    </style>
@endsection

@section('content')

    <h1>API Documentation</h1>
    <h2>How to download replay files</h2>
    <p>All files are stored on <a href="https://aws.amazon.com/s3/">Amazon S3</a> service. Currently it is in
        <a href="http://docs.aws.amazon.com/AmazonS3/latest/dev/RequesterPaysBuckets.html">"Requester pays" mode</a>
        which means that traffic fees are payed by clients that download files instead of website owner. This allows us
        to keep server costs low and run service even with low donations. S3 traffic is <em>free</em> if you download
        to an AWS EC2 instance in EU (Ireland) (eu-west-1) region or $0.09/GB if you download to non-amazon server. A good way for
        you to avoid costs is to launch a free tier EC2 instance, use it to download and analyze replays, and then
        stream results to your main website. In any case you will need an AWS account and authenticate every request
        to download files. Further documentation can be found
        <a href="http://docs.aws.amazon.com/AmazonS3/latest/dev/RequesterPaysBuckets.html">here</a>.</p>
    <p>If we receive enough donations to cover traffic costs for all clients, S3 storage will be switched to free public access.</p>
    <h2>API Methods</h2>
    <p>All requests start with <code>http://hotsapi.net/api/v1</code> prefix.</p>
    <table class="table table-sm table-striped">
        <thead>
        <tr>
            <th>Method</th>
            <th>Url</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>GET</td>
            <td>/replays</td>
            <td>Get list of replays</td>
        </tr>
        <tr>
            <td>GET</td>
            <td>/pagedreplays</td>
            <td>Get a list of paginated replays</td>
        </tr>
        <tr>
            <td>GET</td>
            <td>/replays/{id}</td>
            <td>Get replay details</td>
        </tr>
        <tr>
            <td>POST</td>
            <td>/replays</td>
            <td>Upload a replay</td>
        </tr>
        <tr>
            <td>POST</td>
            <td>/upload</td>
            <td>An alias to POST /replays</td>
        </tr>
        </tbody>
    </table>

    <h3><code>GET /replays</code></h3>
    <p>This method accepts the following GET parameters:</p>
    <table class="table table-sm table-striped">
        <tbody>
        <tr>
            <td>start_date</td>
            <td>Minimum replay date</td>
        </tr>
        <tr>
            <td>end_date</td>
            <td>Maximum replay date</td>
        </tr>
        <tr>
            <td>game_map</td>
            <td>Game map</td>
        </tr>
        <tr>
            <td>game_type</td>
            <td>Game type</td>
        </tr>
        <tr>
            <td>player</td>
            <td>Battle tag of a player</td>
        </tr>
        <tr>
            <td>min_id</td>
            <td>Minimum id in database</td>
        </tr>
        </tbody>
    </table>
    <ul>
        <li>Currently service returns no more than 100 results, use min_id to emulate pagination</li>
        <li>min_id is very handy if you want to regularly query for new replays</li>
    </ul>

    <p>Returns array of replays:</p>
    <table class="table table-sm table-striped">
        <tbody>
        <tr>
            <td>id</td>
            <td>Database id</td>
        </tr>
        <tr>
            <td>filename</td>
            <td>Name of file without path and extension</td>
        </tr>
        <tr>
            <td>url</td>
            <td>Full S3 url of replay file</td>
        </tr>
        <tr>
            <td>size</td>
            <td>File size in bytes</td>
        </tr>
        <tr>
            <td>game_type</td>
            <td>Game type without spaces</td>
        </tr>
        <tr>
            <td>game_date</td>
            <td>Date when game took place</td>
        </tr>
        <tr>
            <td>game_length</td>
            <td>Game length in seconds</td>
        </tr>
        <tr>
            <td>game_map</td>
            <td>Map</td>
        </tr>
        <tr>
            <td>game_version</td>
            <td>HotS client version</td>
        </tr>
        </tbody>
    </table>

    <p>Example:</p>

    <pre><code>GET http://hotsapi.net/api/v1/replays?start_date=2017-03-15&amp;game_map=Battlefield%20of%20Eternity&amp;game_type=HeroLeague&amp;player=poma&amp;min_id=150
</code></pre>

    <pre><code class="language-json">[
  {
    "id": 452,
    "filename": "59a2e9fc744f7",
    "size": 315209,
    "game_type": "HeroLeague",
    "game_date": "2017-07-08 07:32:24",
    "game_length": 538,
    "game_map": "Battlefield of Eternity",
    "game_version": "2.26.2.55010",
    "url": "http://hotsapi.s3-website-eu-west-1.amazonaws.com/59a2e9fc744f7.StormReplay"
  },
  {
    "id": 492,
    "filename": "59a2ea3d501e7",
    "size": 758038,
    "game_type": "HeroLeague",
    "game_date": "2017-07-03 03:45:39",
    "game_length": 1331,
    "game_map": "Battlefield of Eternity",
    "game_version": "2.26.2.55010",
    "url": "http://hotsapi.s3-website-eu-west-1.amazonaws.com/59a2ea3d501e7.StormReplay"
  },
  {
    ...
  }
]
</code></pre>

    <h3><code>GET /replays/paged</code></h3>
    <p>Get all replays, like <code>/replays</code>, but with metadata for pagination</p> 
    <p>This method takes an addition page parameter, indicating the current page number, starting from 1. Empty is assumed to be 1</p>
    <table class="table table-sm table-striped">
        <tbody>
            <tr>
                <td>per_page</td>
                <td>Items per page</td>
            </tr>
            <tr>
                <td>page</td>
                <td>Current page</td>
            </tr>
            <tr>
                <td>page_count</td>
                <td>Total pages available</td>
            </tr>
            <tr>
                <td>total</td>
                <td>Total items available</td>
            </tr>
            <tr>
                <td>Replays</td>
                <td>See /replays above for returned schema</td>
            </tr>
        </tbody>
    </table>

    <h3><code>GET /replays/{id}</code></h3>
    <p>Get replay data by database id. Returns same values as <code>GET /replays</code> with an additional <code>players</code> array:</p>
    <table class="table table-sm table-striped">
        <tbody>
        <tr>
            <td>battletag</td>
            <td>Player battle tag (currently without id)</td>
        </tr>
        <tr>
            <td>hero</td>
            <td>Hero</td>
        </tr>
        <tr>
            <td>hero_level</td>
            <td>Hero level</td>
        </tr>
        <tr>
            <td>team</td>
            <td>Team, can be 0 or 1</td>
        </tr>
        <tr>
            <td>winner</td>
            <td>Boolean whether player won this match</td>
        </tr>
        <tr>
            <td>region</td>
            <td>Region. 1 - US, 2 - EU</td>
        </tr>
        <tr>
            <td>blizz_id</td>
            <td>Internal blizzard player id</td>
        </tr>
        </tbody>
    </table>

    <h3><code>POST /replays</code></h3>
    <p>Accepts a post in usual <code>multipart/form-data</code> format with a <code>file</code> variable containing a replay file</p>
    <p>Response:</p>
    <table class="table table-sm table-striped">
        <tbody>
        <tr>
            <td>success</td>
            <td>Whether an upload was successful</td>
        </tr>
        <tr>
            <td>status</td>
            <td>Upload result (Success, Duplicate, AiDetected, CustomGame, PtrRegion, TooOld, Incomplete)</td>
        </tr>
        <tr>
            <td>id</td>
            <td>database id</td>
        </tr>
        <tr>
            <td>file</td>
            <td>Name of file without path and extension</td>
        </tr>
        <tr>
            <td>url</td>
            <td>Full S3 url of replay file</td>
        </tr>
        </tbody>
    </table>
@endsection
