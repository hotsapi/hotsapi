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
    <a class="btn btn-primary btn-lg" href="{{ $setupLink }}">Download</a>

    <div class="clearfix"></div>
    <hr>

    <h1>Or you can use the web uploader</h1>
    <p>Choose replay files, or drag them onto this page. <span id="container_fileupload_dir_text">Chrome users: If you are uploading &gt;700 replays, please select by directory</span></p>

    <span class="btn btn-success fileinput-button">
        <i class="glyphicon glyphicon-plus"></i>
        <span>Select replays...</span>
        <input id="fileupload" type="file" name="file" multiple>
    </span>

    <span class="btn btn-success fileinput-button" id="container_fileupload_dir">
        <i class="glyphicon glyphicon-plus"></i>
        <span>Select directory...</span>
        <input id="fileupload_dir" type="file" name="file" multiple directory webkitdirectory>
    </span>

    
    <div id="progress" class="progress hidden">
        <div class="progress-bar progress-bar-success"></div>
    </div>

    <table id="files" class="table"><tbody></tbody></table>
@endsection

@section('body')
    <script>
        $(function () {
            $('#fileupload,#fileupload_dir').fileupload({
                url: '/api/v1/replays',
                sequentialUploads: true,
                dataType: 'json',
                drop: function (e, data) {
                    if ($(this).attr('id') == "fileupload_dir") {
                        e.preventDefault();
                        return false;
                    }
                },
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
        });
        
        //disable default full-window drag and drop to avoid triggering both upload widgets
        // $(document).bind('drop dragover', function (e) {
            // e.preventDefault();
            // return false;
        // });
        
        //check for directory-selection compatibility. same test used in Modernizr.
        //repurposed from https://stackoverflow.com/questions/12169585/how-to-detect-directory-select-capability-in-browsers
        function isInputDirSupported() {
            var tmpInput = document.createElement('input');
            if ('webkitdirectory' in tmpInput 
                || 'directory' in tmpInput) return true;
            return false;   
        }
        
        if (isInputDirSupported()) {
            $("#container_fileupload_dir_text,#container_fileupload_dir").show()
        } else {
            $("#container_fileupload_dir_text,#container_fileupload_dir").hide()
        }
    </script>
@endsection