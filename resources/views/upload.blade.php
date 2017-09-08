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
        .upload_status {
            width:100px;
        }
        .upload_status-Success {
            color: #2b542c;
        }
        .upload_status-UploadError {
            color: #411c0e;
        }
        #files > tbody > tr > td.upload_table_progress_container {
            line-height: 0;
            border: none;
            padding-top: 0px;
            padding-bottom: 0px;
        }
        .upload_table_progress_row {
            height: 4px;
        }
        .upload_table_progress {
            height: 2px;
            line-height:2px;
            font-size:2px;
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
    <p>Choose replay files, or drag them onto this page. <span id="container_fileupload_dir_text hidden">Chrome users: If you are uploading &gt;700 replays, please select by directory</span></p>

    <span class="btn btn-success fileinput-button">
        <i class="glyphicon glyphicon-plus"></i>
        <span>Select replays...</span>
        <input id="fileupload" type="file" name="file" multiple>
    </span>

    <span class="btn btn-success fileinput-button hidden" id="container_fileupload_dir">
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
                
                start: function(e) {
                    $('#progress').removeClass('hidden');
                },
             
                drop: function (e, data) {
                    
                    //stop the drag and drop event for the second upload button to avoid triggering it twice
                    if ($(this).attr('id') == 'fileupload_dir') {
                        e.preventDefault();
                        return false;
                    }
                    
                },
                
                done: function (e, data) {
                    
                    let status = data.result.status ? data.result.status : "UploadError";
                    
                    //update status text and add an appropriate class. querySelector stops after the first element is found and should therefore be a little faster than jquery as the table gets larger
                    $(document.querySelector('.upload_status')).addClass('upload_status-'+status).text(status);
                    
                    //remove individual file progressbar (keep the empty row to avoid having the table contents jump around)
                    $('.upload_table_progress').remove();
                    
                },
                
                send: function(e, data) {
                    
                    //add the file to the table once the transfer begins and include a second row for its progress indicator
                    $('#files > tbody').prepend('<tr><td class=upload_filename>'+data.files[0].name+'</td><td class=upload_status>&nbsp;</td></tr><tr class=upload_table_progress_row><td colspan=2 class=upload_table_progress_container><div class="progress-bar upload_table_progress">&nbsp;</div></td></tr>');
               
               },
               
                progress: function(e, data) {
                    
                    //update the individual file progress indicator
                    let progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#files').find('.progress-bar').css('width', progress + '%');
                    
                },
                
                progressall: function (e, data) {
                    
                    //update the overall progress indicator
                    let progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css('width', progress + '%');
                    
                }
            });
        });
        
        //check for directory-selection compatibility. same test used in Modernizr.
        //repurposed from https://stackoverflow.com/questions/12169585/how-to-detect-directory-select-capability-in-browsers
        function isInputDirSupported() {
            var tmpInput = document.createElement('input');
            if ('webkitdirectory' in tmpInput 
                || 'directory' in tmpInput) return true;
            return false;   
        }
        
        if (isInputDirSupported()) {
            $("#container_fileupload_dir_text,#container_fileupload_dir").removeClass('hidden');
        } 
    </script>
@endsection
