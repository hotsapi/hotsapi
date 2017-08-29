@extends('template')
@section('title', 'HotsApi')

@section('head')
    <style>
        .fileinput-button {
            position: relative;
            overflow: hidden;
            display: inline-block;
            margin-bottom: 12px;
        }
        .fileinput-button input {
            position: absolute;
            top: 0;
            right: 0;
            margin: 0;
            opacity: 0;
            -ms-filter: 'alpha(opacity=0)';
            font-size: 200px !important;
            direction: ltr;
            cursor: pointer;
        }

        .upload-Success {
            color: #2b542c;
        }
        .upload-UploadError {
            color: #411c0e;
        }
        .screenshot {
            margin-top: 22px;
        }
    </style>
@endsection

@section('content')


    <img class="pull-right screenshot" src="{{asset('img/uploader.png')}}" width="261px">
    <h1>Replay uploader app</h1>
    <p>The easiest way to upload your replays it to get our uploader app</p>
    <a class="btn btn-primary btn-lg" id="download-setup" href="https://github.com/poma/Hotsapi.Uploader/releases/latest">Download</a>

    <div class="clearfix"></div>
    <hr>

    <h1>Or you can use web uploader</h1>
    <p>Choose files, or drag them onto this page</p>

    <span class="btn btn-success fileinput-button">
        <i class="glyphicon glyphicon-plus"></i>
        <span>Select replays...</span>
        <input id="fileupload" type="file" name="file" multiple>
    </span>

    <div id="progress" class="progress hidden">
        <div class="progress-bar progress-bar-success"></div>
    </div>

    <table id="files" class="table"><tbody></tbody></table>
@endsection

@section('body')
    <script>
        $(function () {
            $('#fileupload').fileupload({
                url: '/api/v1/replays',
                sequentialUploads: true,
                dataType: 'json',
                done: function (e, data) {
                    let status = data.result.status ? data.result.status : "UploadError";
                    $("#files").find('tbody')
                        .prepend($('<tr>')
                            .append($('<td>')
                                .text(data.result.originalName)
                            ).append($('<td>')
                                .text(status)
                                .addClass("upload-" + status)
                            )
                        );
                },
                progressall: function (e, data) {
                    let progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress').removeClass('hidden');
                    $('#progress .progress-bar').css('width', progress + '%');
                }
            });

            // Get direct link to Setup.exe
            $.getJSON("https://api.github.com/repos/poma/Hotsapi.Uploader/releases/latest").done(function (release) {
                $("#download-setup").attr("href", release.assets.find(function (a) { return a.name === "Setup.exe"; }).browser_download_url);
            });
        });
    </script>
@endsection
