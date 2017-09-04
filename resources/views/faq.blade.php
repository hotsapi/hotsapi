@extends('template')
@section('title', 'HotsApi')

@section('content')
    <div class="panel-group" id="accordion">
        <h3>General questions</h3>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-1">Where to get your source code?</a>
                </h4>
            </div>
            <div id="collapse-1" class="panel-collapse collapse in">
                <div class="panel-body">
                    Here are GitHub repos for <a href="https://github.com/poma/hotsapi">website</a>, <a href="https://github.com/poma/hotsapi.chef">deployment scripts</a>, and <a href="https://github.com/poma/Hotsapi.Uploader">uploader</a>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-2">How to download replays from your site?</a>
                </h4>
            </div>
            <div id="collapse-2" class="panel-collapse collapse">
                <div class="panel-body">
                    Currently replays can be downloaded only using API. See <a href="/docs">documentation</a>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-3">How can I support the project?</a>
                </h4>
            </div>
            <div id="collapse-3" class="panel-collapse collapse">
                <div class="panel-body">
                    You can help with service development via <a href="https://github.com/poma/hotsapi">Github</a>, donate via <a href="https://www.patreon.com/hotsapi">Patreon</a>, or just upload your replays to help our database grow
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-4">Do you benefit from replay downloads?</a>
                </h4>
            </div>
            <div id="collapse-4" class="panel-collapse collapse">
                <div class="panel-body">
                    As stated in <a href="http://docs.aws.amazon.com/AmazonDevPay/latest/DevPayDeveloperGuide/S3RequesterPays.html">AWS documentation</a>:
                    <blockquote>
                        The Requester Pays feature (used alone) lets you give other Amazon S3 users access to your data, but you can't make a profit; you can only avoid paying data transfer and request costs.
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
@endsection
